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
 * @copyright Copyright (c) 2014 Max Lazar (http://eec.ms)
 * @link  http://eec.ms/
 */

class Mx_simple_tables extends Mx_simple_tables_base {

	// --------------------------------------------------------------------
	//  PROPERTIES
	// --------------------------------------------------------------------

	/**
	 * Return data
	 *
	 * @access     public
	 * @var        string
	 */
	public $return_data = '';

	public $_dynamic_parameters = array( 'limit', 'collection' );

	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		parent::__construct();
	}


	/**
	 * Helper function for getting a parameter
	 */
	function _get_param( $key, $default_value = '' ) {
		$val = ee()->TMPL->fetch_param( $key );

		if ( $val == '' ) {
			return $default_value;
		}
		return $val;
	}

	/**
	 * Get Entries
	 *
	 * @return [type] [description]
	 */
	public function get_entries() {
		$entries  = 0;
		$where    = array();
		$distinct = array();

		if ( ee()->TMPL->fetch_param( 'dynamic_parameters' ) !== FALSE && ( ! empty( $_POST ) or ! empty( $_GET ) ) ) {
			foreach ( explode( '|', ee()->TMPL->fetch_param( 'dynamic_parameters' ) ) as $var ) {
				if ( ee()->input->get_post( $var ) && in_array( $var, $this->_dynamic_parameters ) ) {
					ee()->TMPL->tagparams[$var] = ee()->input->get_post( $var );
				}

				if ( strncmp( $var, 'search:', 7 ) == 0 && ee()->input->get_post( $var ) ) {

					$where[substr( $var, 7 )] = ee()->input->get_post( $var );
				}
			}
		}
		//


		$collection_short_name = $this->_get_param( 'collection', FALSE );
		$limit = $this->_get_param( 'limit', 999999 );
		$this->_prep_no_results();
		$base =& ee()->TMPL->tagdata;

		if ( $collection_id = $this->get_collection_id( $collection_short_name ) ) {
			$data[0] = array ( 'mx:collection_id' => $collection_id );

			$this->return_data = ee()->TMPL->parse_variables(
				$base,
				$data,
				TRUE
			);


			foreach ( ee()->TMPL->tagparams as $key => $value ) {

				if ( substr( $key, 0, 7 ) == 'search:' ) {
					$search_key = substr( $key, 7 );
					//@todo add column check
					$where[] = $search_key . " = '" . $value . "'";
				}

			}

			$columns_name = ee()->db->list_fields( 'exp_mx_simple_tables_c_'.$collection_id );

			$group_by = $this->_get_param( 'group_by', FALSE );

			if ( ee()->db->table_exists( 'exp_mx_simple_tables_c_'.$collection_id ) ) {
				$sql = "SELECT " . implode( ",", $columns_name ) . " FROM " . "exp_mx_simple_tables_c_" . $collection_id . ($where ? " WHERE " . implode( " AND ", $where ) : '' ) . ( $group_by ? ' GROUP BY ' . $group_by : '' ) . ' LIMIT ' . $limit ;

				$query =  ee()->db->query( $sql );
				/* ee()->db->select( implode(",", $columns_name) )
				->from( 'exp_mx_simple_tables_c_'.$collection_id )->where( $where )->limit( $limit )
				->get();
*/


				$entries = $query->num_rows();
			} else {
				$this->_error_log( 'Is no table for '.$collection_id.' exist.' );
			}

			$this->return_data = ( $entries ) ? ee()->TMPL->parse_variables(
				$this->return_data,
				$query->result_array(),
				TRUE
			) : $this->return_data = ee()->TMPL->no_results;

			if ( ee()->TMPL->fetch_param( 'backspace' ) ) {
				$this->return_data = substr( $this->return_data, 0, - ee()->TMPL->fetch_param( 'backspace' ) );
			}

		} else {
			$this->_error_log( 'Is no collection with name ' . $collection_short_name );
		}

		return $this->return_data;
	}

	/**
	 * Helper for prep _no_results tabs pair
	 *
	 * @return [type] [description]
	 */
	private function _prep_no_results() {
		$td =& ee()->TMPL->tagdata;
		$open = 'if mx_no_results';
		$close = '/if';

		if ( strpos( $td, $open ) !== FALSE && preg_match( '#'.LD.$open.RD.'(.*?)'.LD.$close.RD.'#s', $td, $match ) ) {

			if ( stristr( $match[1], LD.'if' ) ) {
				$match[0] = ee()->functions->full_tag( $match[0], $td, LD.'if', LD.'\/if'.RD );
			}

			ee()->TMPL->no_results = substr( $match[0], strlen( LD.$open.RD ), -strlen( LD.$close.RD ) );


			$td = str_replace( $match[0], '', $td );
		}
	}
	/**
	 * Get collection id by name
	 *
	 * @param [type]  $short_name [description]
	 * @return [type]             [description]
	 */
	public function get_collection_id( $short_name ) {
		if ( count( $this->settings['collections'] ) > 0 ) {
			foreach ( $this->settings['collections'] as $key => $value ) {
				if ( $value['short_name'] == $short_name ) {
					return $key;
				}
			}
		}

		return FALSE;
	}

	/**
	 * Helper funciton for template logging
	 */
	function _error_log( $msg ) {
		ee()->TMPL->log_item( "mx_simple_tables ERROR: ".$msg );
	}
}

/* End of file mod.mx_simple_tables.php */
/* Location: ./system/expressionengine/third_party/mx_simple_tables/mod.mx_simple_tables.php */
