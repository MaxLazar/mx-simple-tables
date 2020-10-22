<?php  if ( ! defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

// include base class
if ( ! class_exists( 'Mx_simple_tables_base' ) ) {
	require_once PATH_THIRD.'mx_simple_tables/base.mx_simple_tables.php';
}

/**
 * -
 *
 * @package  MX Simple Tables
 * @subpackage ThirdParty
 * @category Modules
 * @author    Max Lazar <max@eec.ms>
 * @copyright Copyright (c) 2014 Max Lazar (http://www.eec.ms)
 * @link  http://www.eec.ms/
 */
class Mx_simple_tables_mcp extends Mx_simple_tables_base
{

	/**
	 * Holder for error messages
	 *
	 * @var        string
	 * @access     private
	 */
	private $error_msg = '';


	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		parent::__construct();

		// -------------------------------------
		//  Define base url for module
		// -------------------------------------

		$this->set_base_url();

		// --------------------------------------
		// Add themes url for images
		// --------------------------------------

		$this->data['themes_url'] = ee()->config->slash_item( 'theme_folder_url' );

		// --------------------------------------
		// Load JS lib
		// --------------------------------------

		ee()->load->library( 'javascript' );
	}

	/**
	 * Upload data page
	 *
	 * @return [type] [description]
	 */
	public function upload_data() {

		$this->_set_cp_var( 'cp_page_title', lang( 'upload_data' ) );

		// -------------------------------------
		//  Display error message if any
		// -------------------------------------

		if ( $this->error_msg != '' ) {
			return $this->error_msg;
		}


		if ( $new_settings = ee()->input->post( MX_SIMPLE_TABLES_PACKAGE ) ) {
		}

		return $this->view( 'mcp_upload_data' );

	}

	/**
	 *  Add new collections
	 *
	 * @return [type] [description]
	 */
	public function add_collections() {

		$this->_set_cp_var( 'cp_page_title', lang( 'add_collections' ) );

		// -------------------------------------
		//  Display error message if any
		// -------------------------------------

		if ( $this->error_msg != '' ) {
			return $this->error_msg;
		}


		if ( $new_settings = ee()->input->post( MX_SIMPLE_TABLES_PACKAGE ) ) {



		}

		return $this->view( 'mcp_add_group' );

	}

	/**
	 * Upload data
	 *
	 * @return [type] [description]
	 */
	public function go_upload_data() {
		ini_set( 'upload_max_filesize', '20M' );
		ee()->load->library( 'upload' );
		$path = ee()->upload->_discover_temp_path();
		$config['allowed_types'] = 'xls|csv|xlsx';
		$config['encrypt_name'] = TRUE;
		$config['upload_path'] = $path;


		ee()->upload->initialize( $config );

		if ( !  ee()->upload->do_upload() ) {

			$return_url = $this->base_url. AMP.'method=upload_data'.AMP.'collection_id='.ee()->input->get_post( 'collection_id', false ) ;

			ee()->functions->redirect( $return_url );

		}
		else {
			$upload_data = ee()->upload->data();
			$file_headers = $this->csv_to_headers( $upload_data['full_path'] );
			$collection_id = ee()->input->get_post( 'collection_id' );
			$columns = array();

			$data =  array (
				'headers' => $file_headers,
				'full_path' => $upload_data['full_path']
			);

			if ( !ee()->db->table_exists( 'mx_simple_tables_c_'.$collection_id ) ) {
				$data['columns'] = FALSE;
			} else {
				$sql = ee()->db->query( 'DESCRIBE exp_mx_simple_tables_c_'.$collection_id );
				$columns['do_not_import'] = lang( 'do_not_import' );
				$columns['new_column'] = lang( 'new_column' );
				foreach ( $sql->result_array() as $key => $value ) {
					$columns[$value['Field']] = $value['Field'];
				}
				unset( $columns['entry_id'] );
				$data['columns'] = $columns;
			}

			// $this->csv_to_array( $upload_data['full_path'] );

			$this->settings['file'] = $data;

		}

		$this->_set_cp_var( 'cp_page_title', lang( 'create_collection' ) );

		if ( $this->error_msg != '' ) {
			return $this->error_msg;
		}

		return $this->view( 'mcp_go_upload_data' );
	}

	/**
	 * Show help
	 *
	 * @return [type] [description]
	 */
	public function help() {
		$this->_set_cp_var( 'cp_page_title', lang( 'help' ) );
		return $this->view( 'mcp_help' );
	}

	/**
	 * Save Data
	 *
	 * @return [type] [description]
	 */
	public function save_data() {
		if ( ! empty( $_POST ) ) {
			var_dump( $_POST );
			die();
		}

		$return_url = $this->base_url ;
		ee()->functions->redirect( $return_url );
	}

	/**
	 * Import CSV
	 *
	 * @return [type] [description]
	 */
	public function import_csv() {
		if ( $full_path =  ee()->input->post( 'full_path' ) ) {

			register_shutdown_function( array( $this, 'importShutdown' ) );

			$collection_id = ee()->input->get_post( 'collection_id' );
			$columns = ee()->input->get_post( 'columns' );

			//TEMP solution

			//@todo add check for empty;

			if ( !ee()->db->table_exists( 'mx_simple_tables_c_'.$collection_id ) ) {

				$query = 'CREATE TABLE exp_mx_simple_tables_c_'.$collection_id.' (
							`entry_id` int(10) unsigned NOT NULL auto_increment,
						 ';

				foreach ( $columns as $key => $value ) {
					$query .=  "`column_".$key."` TEXT, ";
					if ($key == 0 OR $key == 3 OR $key == 2 OR $key == 5) {
						$query .=  "`column_".$key."_url_title` TEXT, ";
					}
				}
				$query .= 'PRIMARY KEY (`entry_id`));';

				ee()->db->query( $query );
			};

			//  $data = $this->csv_to_array( $full_path );

			$cols = '';
			foreach ( $columns as $key => $value ) {
				$cols .=  "column_".$key.",";
			}

			$file_headers = $this->csv_to_headers( $full_path );

			$cols = '';
			foreach ( $file_headers[0] as $key => $value ) {
				$index = $key;
				//$index = array_search( $key, $columns );
				//$index = array_search( $value, $columns );
				//$cols .= ( $index !== FALSE ) ? "column_".$index."," : '@dummy,';

				$cols .= ( $index !== FALSE ) ? "column_".$index."," : '@dummy,';
			}


			$sql = "LOAD DATA LOCAL INFILE '$full_path' INTO TABLE exp_mx_simple_tables_c_".$collection_id."
			FIELDS TERMINATED BY ','
    ENCLOSED BY '\"'
    ESCAPED BY ''
    LINES TERMINATED BY '\\r'
    IGNORE 1 LINES (".trim( $cols, "," ).");";

		//	$sql = "LOAD DATA LOCAL INFILE '$full_path' INTO TABLE exp_mx_simple_tables_c_".$collection_id." FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\\r' IGNORE 1 LINES (".trim( $cols, "," ).");";

			ee()->db->query( $sql );
		//	var_dump($sql);
		//	die();
			$sql   = "SELECT entry_id, column_0, column_3, column_2 , column_5 FROM exp_mx_simple_tables_c_".$collection_id . " LIMIT 99999999";
			$query =  ee()->db->query( $sql );

			if ( $query->num_rows() > 0 ) {

				$index =  0;
				$data = array();

				foreach ( $query->result_array() as $key => $value ) {
					if ( $index  > 10 ) {
						ee()->db->update_batch( "exp_mx_simple_tables_c_" . $collection_id , $data, 'entry_id' );
						$data = array();
						$index = 0;
					}

					$data[$index]['entry_id'] = $value['entry_id'];
					$data[$index]['column_0_url_title'] = $this->url_title( $value['column_0'], 'dash', TRUE );
					$data[$index]['column_3_url_title'] = $this->url_title( $value['column_3'], 'dash', TRUE );
					$data[$index]['column_2_url_title'] = $this->url_title( $value['column_2'], 'dash', TRUE );
					$data[$index]['column_5_url_title'] = $this->url_title( $value['column_5'], 'dash', TRUE );
					$index++;
				}
				if ( $index  > 0 ) {
						ee()->db->update_batch( "exp_mx_simple_tables_c_" . $collection_id , $data, 'entry_id' );
						$data = array();
						$index = 0;
				}
			}
		}

		// ESCAPED BY ''  $sql = "LOAD DATA LOCAL INFILE 'xfile.txt' INTO TABLE `lettings` FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\\n' IGNORE 1 LINES;";

		$return_url = $this->base_url ;

		ee()->functions->redirect( $return_url );

	}
	/**
	 * [url_title description]
	 *
	 * @param [type]  $str       [description]
	 * @param string  $separator [description]
	 * @param boolean $lowercase [description]
	 * @return [type]             [description]
	 */
	private function url_title( $str, $separator = 'dash', $lowercase = FALSE ) {
		if ( UTF8_ENABLED ) {
			$CI =& get_instance();
			$CI->load->helper( 'text' );

			$str = utf8_decode( $str );
			$str = preg_replace_callback( '/(.)/', 'convert_accented_characters', $str );
		}

		$separator = ( $separator == 'dash' ) ? '-' : '_';

		$trans = array(
			'&\#\d+?;'     => '',
			'&\S+?;'     => '',
			'\s+|/+'     => $separator,
			'[^a-z0-9\-\._]'   => '',
			$separator.'+'    => $separator,
			'^[-_]+|[-_]+$'    => '',
			'\.+$'      => ''
		);

		$str = strip_tags( $str );

		foreach ( $trans as $key => $val ) {
			$str = preg_replace( "#".$key."#i", $val, $str );
		}

		if ( $lowercase === TRUE ) {
			$str = strtolower( $str );
		}

		return trim( stripslashes( $str ) );
	}


	/**
	 * [importShutdown description]
	 *
	 * @return [type] [description]
	 */
	public function importShutdown() {
		//die( 'too big ' );
	}

	/**
	 * Convert CSV to array
	 *
	 * @param string  $filename  [description]
	 * @param string  $delimiter [description]
	 * @return [type]            [description]
	 */
	public function csv_to_array( $filename='', $delimiter=',' ) {

		if ( !file_exists( $filename ) || !is_readable( $filename ) )
			return FALSE;

		$header = NULL;
		$data = array();
		if ( ( $handle = fopen( $filename, 'r' ) ) !== FALSE ) {
			while ( ( $row = fgetcsv( $handle, 1000, $delimiter ) ) !== FALSE ) {
				if ( !$header )
					$header = $row;
				else
					$data[] = array_combine( $header, $row );
			}
			fclose( $handle );
		}
		return $data;
	}

	/**
	 * Get Headers from CSV
	 *
	 * @param string  $filename  [description]
	 * @param string  $delimiter [description]
	 * @return [type]            [description]
	 */
	public function csv_to_headers( $filename='', $delimiter=',' ) {
		if ( !file_exists( $filename ) || !is_readable( $filename ) )
			return FALSE;

		$header = NULL;
		$data = array();
		if ( ( $handle = fopen( $filename, 'r' ) ) !== FALSE ) {
			$row = fgetcsv( $handle, 1000, $delimiter );

			$data[] = $row ;

			fclose( $handle );
		}

		return $data;
	}

	/**
	 * Index Page
	 *
	 * @return [type] [description]
	 */
	public function index() {

		$this->_set_cp_var( 'cp_page_title', lang( 'list' ) );

		// -------------------------------------
		//  Display error message if any
		// -------------------------------------

		if ( $this->error_msg != '' ) {
			return $this->error_msg;
		}

		return $this->view( 'mcp_index' );

	}

	/**
	 * Collection list
	 *
	 * @return [type] [description]
	 */
	public function collection() {

		$this->_set_cp_var( 'cp_page_title', lang( 'collection' ) );

		// -------------------------------------
		//  Display error message if any
		// -------------------------------------

		if ( $this->error_msg != '' ) {
			return $this->error_msg;
		}

		return $this->view( 'mcp_collection' );
	}

	/**
	 * Delete collection
	 *
	 * @return [type] [description]
	 */
	public function delete() {
		$collection_id = ee()->input->get_post( 'collection_id' );
		unset( $this->settings['collections'][$collection_id] );

		$this->saveSettingsToDB( $this->settings );

		ee()->load->dbforge();
		ee()->dbforge->drop_table( 'mx_simple_tables_c_'.$collection_id );

		$return_url = $this->base_url ;

		ee()->functions->redirect( $return_url );
	}

	/**
	 * Save Settings
	 *
	 * @return [type] [description]
	 */
	public function save_settings() {

		if ( $new_settings = ee()->input->post( MX_SIMPLE_TABLES_PACKAGE ) ) {

			if ( isset( $new_settings['new_name'] ) ) {
				$data['collections'] = ( isset( $this->settings['collections'] ) ) ?
					$this->settings['collections'] : array();
				$collection_id = count( $data['collections'] ) + 1;
				$data['collections'][$collection_id]['name'] = $new_settings['new_name'];
				$data['collections'][$collection_id]['short_name'] = $new_settings['new_short_name'];
				$this->settings = array_merge( $this->settings, $data );
			}

			$this->saveSettingsToDB( $this->settings );

			$this->_ee_notice( ee()->lang->line( 'extension_settings_saved_success' ) );
		}

		$return_url = $this->base_url ;

		ee()->functions->redirect( $return_url );

	}

	/**
	 * Set cp var
	 *
	 * @access     private
	 * @param string
	 * @param string
	 * @return     void
	 */
	private function _set_cp_var( $key, $val ) {
		if ( version_compare( APP_VER, '2.6.0', '<' ) ) {
			ee()->cp->set_variable( $key, $val );
		}
		else {
			ee()->view->$key = $val;
		}
	}

}

/* End of file mcp.mx_simple_tables.php */
/* Location: ./system/expressionengine/third_party/mx_simple_tables/mcp.mx_simple_tables.php */
