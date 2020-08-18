<?php
/**
 * Class that defines the harvesting process.
 *
 * @package MITlib Pull Hours
 * @since 0.0.1
 */

namespace Mitlib;

use GuzzleHttp\Client;

/**
 * Defines base widget
 */
class Pull_Hours_Harvester {

	/**
	 * The backup folder is where we copy old versions of downloaded data,
	 * in folders named by the cache_timestamp.
	 *
	 * @var string The local folder in which old data is backed up.
	 */
	public $backup_folder = 'backups/';

	/**
	 * The cache_timestamp value is assigned at the start of the harvest
	 * operation, and is stored as a WordPress "option". It is used to name
	 * the backup directory, and showed to site builders as the time that the
	 * hours information was last downloaded.
	 *
	 * @var integer A UNIX timestamp of the time when harvesting starts.
	 */
	public $cache_timestamp = '';

	/**
	 * The data folder is where we write the current set of hours information
	 * for use by the Parent theme.
	 *
	 * @var string The local folder in which newly harvested data is written.
	 */
	public $data_folder = '';

	/**
	 * The Google API key is a credential used to access the Google Sheets
	 * API. It should be restricted to only the Sheets API, as well as
	 * restricted to referals from Libraries domains.
	 *
	 * @var string The API key used to connect to the Google Sheets API.
	 */
	public $google_api_key = '';

	/**
	 * The path is the loal file path to the current directory. It is
	 * populated by the WordPress function plugin_dir_path(), and is necessary
	 * because relative file paths fail under WordPress.
	 *
	 * @var string The full path to the current directory.
	 */
	public $path = '';

	/**
	 * The spreadsheet_key is defined by site builders, stored in a WordPress
	 * "option", and stored locally here to define both the URLs to be
	 * harvested and as filenames for the harvested data.
	 *
	 * @var string The key of the Google spreadsheet being harvested.
	 */
	public $spreadsheet_key = '1hK_4p-jx7dxW3RViRcBDSF_4En2QGgxx-Zy7zXkNIQg';

	/**
	 * This is the only public method in the harvester object, and controls
	 * everything that happens during harvest.
	 */
	public function harvest() {

		// First, we set the canonical time for all of these operations.
		// This is used to name the backup folder, as well as to report the
		// age of the cache to the user.
		$this->set_properties();

		// Second, back up the old cache.
		// This happens by copying the contents of the data/ folder to a
		// timestamped folder inside the backups/ folder.
		$this->backup();

		// Third, we establish a client that can retrieve information from the
		// Google Sheets API (v4).
		// This approach is borrowed from the PHP Quickstart at
		// https://developers.google.com/sheets/api/quickstart/php
		$api_service = $this->get_service();

		// Now we build an associate array of the materials that need to be
		// harvested from Google Sheets. We start with the spreadsheet key,
		// stored in $this->spreadsheet_key.
		// The spreadsheet key is harvested itself, as are separate URLs for
		// each worksheet in the spreadsheet.
		// At the end of this operation, $sheet_list has an associative array
		// along these lines:
		// - key => filename in cache
		// - value => url that will be polled.

		// Fourth, we populate an array of sheet names within our spreadsheet.
		// This array will then comprise the "shopping list" of values that
		// are harvested to populate the local cache.
		$response = $api_service->spreadsheets->get( $this->spreadsheet_key );
		$sheet_array = array_map( array( $this, 'pluck_sheet_name' ) , $response->sheets );

		// Fifth, we iterates over the associative array that was built in the
		// last step, reading each URL (in the value) and saving the contents
		// to the cache (named in the key).
		$this->fetch( $api_service, $sheet_array );

		// Finally, we echo an updated message about the number of records
		// that were updated.
		echo( '<div class="updated"><p>Local hours cache has been harvested with information from ' . esc_html( count( $sheet_array ) ) . ' sheets .</p></div>' );

	}

	/**
	 * This method makes a backup of the old version of the harvested data.
	 * Backups are stored in timestamped directories inside the path defined
	 * by $backup_folder.
	 */
	private function backup() {
		// Construct full paths to backup and data folders.
		$folder = strftime( '%Y%m%d-%H%M%S', $this->cache_timestamp );
		$this->backup_folder = $this->path . '/' . $this->backup_folder . $folder . '/';
		$this->data_folder = $this->path . '/' . $this->data_folder;

		// Create that folder.
		mkdir( $this->backup_folder );

		// Get list of files to back up.
		$files = scandir( $this->data_folder );

		// Copy those files into backup folder, but...
		foreach ( $files as $file ) {
			// ... skip files that do not end in ".json".
			if ( substr( $file, -5 ) <> '.json' ) {
				continue;
			}
			copy(
				$this->data_folder . $file,
				$this->backup_folder . $file
			);
		}
	}

	/**
	 * This method iterates over the $array (defined in build_sheet_list) and
	 * fetches each item (in the $value) and writes it to the filename defined
	 * by $target (which has been sanitized with dashes).
	 *
	 * @param Array $array An associative array of filenames and URLs.
	 */
	private function fetch( $service, $array ) {
		foreach ( $array as $target ) {
			$data = $service->spreadsheets_values->get( $this->spreadsheet_key, $target );
			$filename = sanitize_file_name( $target ) . '.json';
			$this->write( $filename, json_encode( $data['values'] ) );
		}
	}

	/**
	 * This method establishes a client, with associated token, that can read
	 * information from the Google Sheets API (v4).
	 *
	 * @link https://developers.google.com/sheets/api/quickstart/php
	 * @link https://github.com/googleapis/google-api-php-client#user-content-controlling-http-client-configuration-directly
	 */
	private function get_service() {
		$httpClient = new Client([
			'headers' => [
				'referer' => DOMAIN_CURRENT_SITE
			]
		]);
		$client = new \Google_Client();
		$client->setApplicationName('LibraryHoursSheets');
		$client->setDeveloperKey( $this->google_api_key );
		$client->setHttpClient($httpClient);
		$service = new \Google_Service_Sheets( $client );
		return $service;
	}

	/**
	 * This method plucks the name of a sheet out of general metadata for a
	 * spreadsheet.
	 *
	 * @return string $name The name of a single sheet in a spreadsheet.
	 */
	private function pluck_sheet_name( $haystack ) {
		return $haystack['properties']['title'];
	}

	/**
	 * This method populates cache_timestamp and spreadsheet_key based on the
	 * current timestamp, and the WordPress option for the harvested
	 * spreadsheet.
	 */
	private function set_properties() {
		// Define timestamp as current time.
		$this->cache_timestamp = time();
		update_option( 'cache_timestamp', $this->cache_timestamp );

		// Populate spreadsheet key based on WP option.
		$this->google_api_key = get_option( 'google_api_key' );

		// Populate spreadsheet key based on WP option.
		$this->spreadsheet_key = get_option( 'spreadsheet_key' );

		// Define path to data storage.
		// This used to use plugin_dir_path( __FILE__ ) but we decided to
		// instead use the already-in-use /app/libhours-buildhours/ path.
		$this->path = get_home_path() . 'app/libhours-buildjson';
	}

	/**
	 * This method writes a provided piece of data to the specified filename,
	 * in a directory defined by $this->data_folder.
	 *
	 * @param string $filename The name of the file to be written.
	 * @param string $data The contents of the file to be written.
	 */
	private function write( $filename, $data ) {
		file_put_contents( $this->data_folder . $filename, $data );
	}
}
