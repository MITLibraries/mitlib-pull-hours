<?php

date_default_timezone_set( 'America/New_York' );

class Scratch {

	public $backup_folder = 'backups/';

	public $cache_timestamp = '';

	public $data_folder = 'data/';

	public $spreadsheet_key = '1hK_4p-jx7dxW3RViRcBDSF_4En2QGgxx-Zy7zXkNIQg';

	// TODO: The scandir() function returns entries for the current and parent
	// directories, which then cause problems when the copy() function tries
	// to act upon them. These need to be filtered out somehow.
	public function backup() {
		// Format the timestamp into a folder name.
		$folder = strftime( '%Y%m%d-%H%M%S', $this->cache_timestamp );
		$this->backup_folder = $this->backup_folder . $folder . '/';
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

	// TODO: This needs to have the parent sheet URL added to it.
	// TODO: The child sheets need to be different formatted.
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

		// This is for debugging only.
		echo( '<pre>' );
		print_r( $sheets_array );
		echo( '</pre>' );

		return $sheets_array;
	}

	private function fetch( $array ) {
		foreach ( $array as $key=>$value ) {
			// echo( '<p>Fetching: ' . $value . '</p>' );
			$data = file_get_contents( $value );
			$this->write( $key, $data );
		}

	}

	public function harvest() {

		// First, we set the canonical time for all of these operations.
		// This is used to name the backup folder, as well as to report the
		// age of the cache to the user.
		$this->set_timestamp();

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

		// Report status
		$this->status();
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

	private function lookup_format( $url, $format ) {
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

	private function set_sheet_key( $url ) {
		$length = strlen( $url );
		$position = strrpos( $url, '/' ) + 1;
		return substr( $url, $position - $length );
	}

	private function set_timestamp() {
		$this->cache_timestamp = time();
	}

	private function status() {
		echo '<p>Information about library hours was last updated:<br />' .
		     strftime( '%b %e, %Y at %r ', $this->cache_timestamp ) . '( ago)</p>';
		echo '<p>' . $this->cache_timestamp . '</p>';
	}

	private function write($filename, $data) {
		file_put_contents( 'data/' . $filename, $data );
	}
}

$foo = New Scratch();
$foo->harvest();
