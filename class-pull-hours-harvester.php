<?php
/**
 * Class that defines the harvesting process.
 *
 * @package MITlib Pull Hours
 * @since 0.0.1
 */

namespace mitlib;

/**
 * Defines base widget
 */
class Pull_Hours_Harvester {

	public $backup_folder = 'backups/';

	public $cache_timestamp = '';

	public $data_folder = 'data/';

	public $path = '';

	public $spreadsheet_key = '1hK_4p-jx7dxW3RViRcBDSF_4En2QGgxx-Zy7zXkNIQg';

	public function backup() {
		// Figure out where we are
		$this->path = plugin_dir_path( __FILE__ );

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
			// This is meant to just skip short entries - i.e. '.' and '..'
			if ( strlen( $file ) < 5 ) {
				continue;
			}
			copy(
				$this->data_folder . $file,
				$this->backup_folder . $file
			);
		}
	}

	private function build_sheet_list( $key ) {
		// $sheets_array is the list of URLs that will be harvested and
		// written to the data cache.
		$sheets_array = Array();

		// Push the master URL
		$sheets_array[ $this->spreadsheet_key ] = $this->lookup_base_url( $this->spreadsheet_key , 'tabletop' );

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

	private function fetch( $array ) {
		foreach ( $array as $key=>$value ) {
			$data = file_get_contents( $value );
			$this->write( $key, $data );
		}
	}

	public function harvest() {

		// First, we set the canonical time for all of these operations.
		// This is used to name the backup folder, as well as to report the
		// age of the cache to the user.
		$this->set_properties();

		// Second, back up the old cache.
		// This happens by copying the contents of the data/ folder to a
		// timestamped folder inside the backups/ folder.
		$this->backup();

		// Now we build an associate array of the materials that need to be
		// harvested from Google Sheets. We start with the spreadsheet key,
		// stored in $this->spreadsheet_key.
		// The spreadsheet key is harvested itself, as are separate URLs for
		// each worksheet in the spreadsheet.
		// At the end of this operation, $sheet_list has an associative array
		// along these lines:
		// - key => filename in cache
		// - value => url that will be polled
		$sheet_list = $this->build_sheet_list( $this->spreadsheet_key );

		// This iterates over the associative array that was built in the last
		// step, reading each URL (in the value) and saving the contents to
		// the cache (named in the key).
		$this->fetch( $sheet_list );

		// Write new contents.
		// $foobar = $this->lookup_base_url( $this->spreadsheet_key, 'tabletop' );
		// $this->write(
		//	$this->spreadsheet_key,
		//	file_get_contents( $foobar )
		//);
	}

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

	private function lookup_child_url( $key ) {
		$url = 'https://spreadsheets.google.com/feeds/list/' .
			   $this->spreadsheet_key . '/' . $key .
			   '/public/values' .
			   '?alt=json-in-script&callback=Tabletop.singleton.loadSheet';
		return $url;
	}

	private function set_sheet_key( $url ) {
		$length = strlen( $url );
		$position = strrpos( $url, '/' ) + 1;
		return substr( $url, $position - $length );
	}

	private function set_properties() {
		// Define timestamp as current time.
		$this->cache_timestamp = time();
		update_option( 'cache_timestamp', $this->cache_timestamp );

		// Populate spreadsheet key based on WP option.
		$this->spreadsheet_key = get_option( 'spreadsheet_key' );
	}

	private function write($filename, $data) {
		file_put_contents( $this->data_folder . $filename, $data );
	}
}
