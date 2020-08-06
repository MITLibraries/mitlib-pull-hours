<?php
/**
 * Class that defines the harvesting process.
 *
 * @package MITlib Pull Hours
 * @since 0.0.1
 */

namespace Mitlib;

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

		// Now we establish a client that can retrieve information from the
		// Google Sheets API (v4).
		// This approach is borrowed from the PHP Quickstart at
		// https://developers.google.com/sheets/api/quickstart/php
		$api_client = $this->get_client();
		var_dump( $api_client );

		// Now we build an associate array of the materials that need to be
		// harvested from Google Sheets. We start with the spreadsheet key,
		// stored in $this->spreadsheet_key.
		// The spreadsheet key is harvested itself, as are separate URLs for
		// each worksheet in the spreadsheet.
		// At the end of this operation, $sheet_list has an associative array
		// along these lines:
		// - key => filename in cache
		// - value => url that will be polled.
		$sheet_list = $this->build_sheet_list( $this->spreadsheet_key );

		// This iterates over the associative array that was built in the last
		// step, reading each URL (in the value) and saving the contents to
		// the cache (named in the key).
		$this->fetch( $sheet_list );

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

		// Copy those files into backup folder.
		foreach ( $files as $file ) {
			// This is meant to just skip short entries - i.e. '.' and '..'.
			if ( strlen( $file ) < strlen( $this->spreadsheet_key ) ) {
				continue;
			}
			copy(
				$this->data_folder . $file,
				$this->backup_folder . $file
			);
		}
	}

	/**
	 * This method populates an assocative array based on the discovered
	 * contents of a Google spreadsheet identified by $key (which ultimately
	 * is defined by site editors and stored in the $spreadsheet_key option).
	 *
	 * @param String $key An identifying key to a Google spreadsheet.
	 */
	private function build_sheet_list( $key ) {
		// $sheets_array is the list of URLs that will be harvested and
		// written to the data cache.
		$sheets_array = array();

		// Store the parent sheet URL.
		$sheets_array[ $this->spreadsheet_key ] = $this->lookup_base_url( $this->spreadsheet_key, 'tabletop' );

		// This is the master URL that we use to look up the rest.
		$url = $this->lookup_base_url( $key, 'json' );
		$base = json_decode( file_get_contents( $url ), true );

		foreach ( $base['feed']['entry'] as $item ) {
			$sheet_key = $this->set_sheet_key( $item['id']['$t'] );
			$sheet_url = $this->lookup_child_url( $sheet_key );
			$sheets_array[ $this->spreadsheet_key . '-' . $sheet_key ] = $sheet_url;
		}

		return $sheets_array;
	}

	/**
	 * This method iterates over the $array (defined in build_sheet_list) and
	 * fetches each item (in the $value) and writes it to the filename defined
	 * by $key.
	 *
	 * @param Array $array An associative array of filenames and URLs.
	 */
	private function fetch( $array ) {
		foreach ( $array as $key => $value ) {
			$data = file_get_contents( $value );
			$this->write( $key, $data );
		}
	}

	/**
	 * This method establishes a client, with associated token, that can read
	 * information from the Google Sheets API (v4).
	 *
	 * @link https://developers.google.com/sheets/api/quickstart/php
	 */
	private function get_client() {
		error_log( 'This will be the start of the v4 API harvester...' );
		$client = new \Google_Client();
		return $client;
	}

	/**
	 * This method returns the URL to a Google spreadsheet - not a specific
	 * worksheet, but the overall entity. Because we end up pulling this
	 * information twice in different formats, we have an argument to
	 * identify each.
	 *
	 * @param string $key An identifying key to a Google spreadsheet.
	 * @param string $format Either 'json' or 'tabletop'.
	 */
	private function lookup_base_url( $key, $format ) {
		$url = 'https://spreadsheets.google.com/feeds/worksheets/' .
			   $key .
			   '/public/basic';
		switch ( $format ) {
			case 'json':
				$url .= '?alt=json';
				break;
			case 'tabletop':
				$url .= '?alt=json-in-script&callback=Tabletop.singleton.loadSheets';
				break;
		}
		return $url;
	}

	/**
	 * This method returns the URL to a specific worksheet inside a Google
	 * spreadsheet.
	 *
	 * @param string $key An identifying key to the specific worksheet inside
	 * a Google spreadsheet.
	 */
	private function lookup_child_url( $key ) {
		$url = 'https://spreadsheets.google.com/feeds/list/' .
			   $this->spreadsheet_key . '/' . $key .
			   '/public/values' .
			   '?alt=json-in-script&callback=Tabletop.singleton.loadSheet';
		return $url;
	}

	/**
	 * This method identifies the key to a specific worksheet, which is the
	 * last few characters in a retrieved URL, after the final slash.
	 *
	 * @param string $url A URL to a Google spreadsheet worksheet.
	 */
	private function set_sheet_key( $url ) {
		$length = strlen( $url );
		$position = strrpos( $url, '/' ) + 1;
		return substr( $url, $position - $length );
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
