<?php

class Scratch {

	public $backup_folder = 'backups/';

	public $cache_timestamp = '';

	public $data_folder = 'data/';

	public $spreadsheet_key = '1hK_4p-jx7dxW3RViRcBDSF_4En2QGgxx-Zy7zXkNIQg';

	private function echo_r( $thing ) {
		echo( '<pre>' );
		print_r( $thing );
		echo( '</pre>' );
	}

	private function echo( $message ) {
		echo( '<p>' . $message . '</p>' );
	}

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
			// If this file isn't a json file we skip
			if ( strpos( $file, '.json' ) == 0 ) {
				continue;
			}
			copy(
				$this->data_folder . $file,
				$this->backup_folder . $file
			);
		}
	}

	private function build_sheet_list( $key ) {
		$sheets_array = Array();
		$url = $this->lookup_base_url( $key, 'json' );
		$base = json_decode( file_get_contents( $url ), true );
		foreach ( $base['feed']['entry'] as $item ) {
			array_push( $sheets_array, $item['id']['$t'] );
		}
		return $sheets_array;
	}

	private function fetch( $list ) {
		foreach ( $list as $item ) {
			$item_key = $this->set_sheet_key( $item );
			$url = $this->lookup_format( $item, 'tabletop' );
			$data = file_get_contents( $url );
			$this->write( $this->spreadsheet_key . '-' . $item_key, $data );
		}

	}

	public function harvest() {

		// First, we set the canonical time for all of these operations;
		$this->set_timestamp();

		// First, back up the old cache.
		$this->backup();

		// Option 1:
		// - Get the base sheet as JSON for parsing
		// - Extract the list of base and data sheets for retrieving
		// - Iterate over the list, downloading all sheets to data/
		$sheet_list = $this->build_sheet_list( $this->spreadsheet_key );

		$this->fetch( $sheet_list );

		// Write new contents.
		$foobar = $this->lookup_base_url( $this->spreadsheet_key, 'tabletop' );
		$this->write(
			$this->spreadsheet_key,
			file_get_contents( $foobar )
		);

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
		file_put_contents( 'data/' . $filename . '.json', $data );
	}
}

echo( 'Here goes...' );
$foo = New Scratch();
$foo->harvest();
