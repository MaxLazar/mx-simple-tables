<?php  if ( ! defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

require_once PATH_THIRD . 'mx_simple_tables/config.php';

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
class Mx_simple_tables_base {

	// --------------------------------------------------------------------
	// PROPERTIES
	// --------------------------------------------------------------------

	/**
	 * Add-on name
	 *
	 * @var        string
	 * @access     public
	 */
	public $name = MX_SIMPLE_TABLES_NAME;

	/**
	 * Add-on version
	 *
	 * @var        string
	 * @access     public
	 */
	public $version = MX_SIMPLE_TABLES_VERSION;

	/**
	 * URL to module docs
	 *
	 * @var        string
	 * @access     public
	 */
	public $docs_url = MX_SIMPLE_TABLES_DOCS;

	/**
	 * Settings array
	 *
	 * @var        array
	 * @access     public
	 */
	public $settings = array();

	// --------------------------------------------------------------------

	/**
	 * Package name
	 *
	 * @var        string
	 * @access     protected
	 */
	protected $package = MX_SIMPLE_TABLES_PACKAGE;

	/**
	 * Main class name
	 *
	 * @var        string
	 * @access     protected
	 */
	protected $class_name;

	/**
	 * Site id shortcut
	 *
	 * @var        int
	 * @access     protected
	 */
	protected $site_id;

	/**
	 * Base url for module
	 *
	 * @var        string
	 * @access     protected
	 */
	protected $base_url;

	/**
	 * Base url for extension
	 *
	 * @var        string
	 * @access     protected
	 */
	protected $ext_url;

	/**
	 * Data array for views
	 *
	 * @var        array
	 * @access     protected
	 */
	protected $data = array();

	/**
	 * Default settings array
	 *
	 * @var        array
	 * @access     protected
	 */
	protected $default_settings = array(
	);

	/**
	 * Extra nav in CP
	 *
	 * @var        array
	 * @access     protected
	 */
	protected $extra_nav = array();

	// --------------------------------------------------------------------

	/**
	 * Control Panel assets
	 *
	 * @var        array
	 * @access     private
	 */
	private $mcp_assets = array(
		'css/handsontable.full.min.css',
		'js/handsontable.full.min.js',
		'css/simple.table.style.css'
	);
	/**
	 * Per page
	 * @var integer
	 */
	public $perpage = 30;

	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		// -------------------------------------
		//  Define the package path
		// -------------------------------------

		ee()->load->add_package_path( PATH_THIRD.$this->package );

		// -------------------------------------
		//  Load helper
		// -------------------------------------

		ee()->load->helper( $this->package );

		// -------------------------------------
		//  Libraries
		// -------------------------------------

		// ee()->load->library('Mx_simple_tables_model');

		// -------------------------------------
		//  Load the models
		// -------------------------------------

		// Mx_simple_tables_model::load_models();

		// -------------------------------------
		//  Set main class name
		// -------------------------------------

		$this->class_name = ucfirst( $this->package );

		// -------------------------------------
		//  Get site shortcut
		// -------------------------------------

		$this->site_id = (int) ee()->config->item( 'site_id' );

		$this->settings = $this->get_settings();


	}

	/**
	 * _ee_notice function.
	 *
	 * @access private
	 * @param mixed   $msg
	 * @return void
	 */
	function _ee_notice( $msg ) {
		ee()->javascript->output( array(
				'$.ee_notice("'.ee()->lang->line( $msg ).'",{type:"success",open:true});',
				'window.setTimeout(function(){$.ee_notice.destroy()}, 3000);'
			) );
	}


	/**
	 * Sets base url for views
	 *
	 * @access     protected
	 * @return     void
	 */
	protected function set_base_url() {
		$this->base_url = $this->data['base_url'] = function_exists( 'cp_url' )
			? cp_url( 'addons_modules/show_module_cp', array( 'module' => $this->package ) )
			: BASE.AMP.'C=addons_modules&amp;M=show_module_cp&amp;module='.$this->package;

		$this->ext_url = $this->data['ext_url'] = function_exists( 'cp_url' )
			? cp_url( 'addons_extensions/extension_settings', array( 'file' => $this->package ) )
			: BASE.AMP.'C=addons_extensions&amp;M=extension_settings&amp;file='.$this->package;
	}

	/**
	 * Saves the specified settings array to the database.
	 *
	 * @since Version 1.0.0
	 * @access protected
	 * @param array   $settings an array of settings to save to the database.
	 * @return void
	 * */
	protected function saveSettingsToDB( $settings ) {
		ee()->db->where( 'module_name', $this->class_name )
		->update( 'modules', array( 'settings' => serialize( $settings ) ) );

		$this->settings = $this->get_settings();

	}

	/**
	 * Get settings
	 *
	 * @access     protected
	 * @param string
	 * @return     mixed
	 */
	protected function get_settings( $which = FALSE ) {
		if ( empty( $this->settings ) ) {

			if ( !ee()->db->field_exists( 'settings', 'modules' ) ) {
				ee()->load->dbforge();
				$column = array( 'settings'  => array( 'type' => 'TEXT' ) );
				ee()->dbforge->add_column( 'modules', $column );
			}

			// Check cache
			if ( ( $this->settings = mx_get_cache( $this->package, 'settings' ) ) === FALSE ) {
				// Not in cache? Get from DB and add to cache
				$query = ee()->db->select( 'settings' )
				->from( 'modules' )
				->where( 'module_name', $this->class_name )
				->limit( 1 )
				->get();

				$this->settings = (array) @unserialize( $query->row( 'settings' ) );

				// Add to cache
				//mx_set_cache($this->package, 'settings', $this->settings);
			}
		}

		// Always fallback to default settings
		$this->settings = array_merge( $this->default_settings, $this->settings );

		if ( $which !== FALSE ) {
			return isset( $this->settings[$which] ) ? $this->settings[$which] : FALSE;
		}
		else {
			return $this->settings;
		}
	}

	/**
	 * View add-on page
	 *
	 * @access     protected
	 * @param string
	 * @return     string
	 */
	protected function view( $file ) {


		// -------------------------------------
		// Adds the XID / CSRF_TOKEN data to the view
		// -------------------------------------

		ee()->load->library( 'javascript' );

		$this->data['csrf_token_name']        = defined( 'CSRF_TOKEN' ) ? 'csrf_token' : 'XID';
		$this->data['csrf_token_value']       = defined( 'CSRF_TOKEN' ) ? CSRF_TOKEN : XID_SECURE_HASH;
		$this->data['encrypt_key_set']        = ( ee()->config->item( 'encryption_key' ) != '' ) ? TRUE : FALSE;
		$this->data['form_post_url']          = $this->base_url . AMP. 'method=save_settings';
		$this->data['form_upload_url']        = $this->base_url.AMP.'method=go_upload_data';
		$this->data['form_collection_filter'] = $this->base_url.AMP.'method=collection'.AMP.	'collection_id='.ee()->input->get_post( 'collection_id', 0);
		$this->data['base_url']               = $this->base_url;
		$this->data['input_prefix']           = MX_SIMPLE_TABLES_PACKAGE;
		$this->data['settings']               = array();
		$this->data['offset']              	  = ee()->input->get_post( 'rownum', false );

		$data_settings = array (
			'collections'    => array(),
			'new_short_name' => '',
			'new_name'       => '',
			'pagination'     => FALSE,
			'collection_id'  => ee()->input->get_post( 'collection_id', false ),
			'entries'        => array()
		);

		$this->data['settings'] = array_merge($data_settings, $this->data['settings']);

		if ( isset( $this->settings['collections'] ) && count( $this->settings['collections'] ) > 0 ) {
			foreach ( $this->settings['collections'] as $key => $value ) {
				if ( ee()->db->table_exists( 'exp_mx_simple_tables_c_'.$key ) ) {
					$query = ee()->db->select( 'COUNT(*) as count' )
					->from( 'exp_mx_simple_tables_c_'.$key )->limit( 1 )
					->get()->row();
					$this->data['settings']['collections'][$key]['entries'] = $query->count;
				} else {
					$this->data['settings']['collections'][$key]['entries'] = 0;
				}

				$this->data['settings']['collections'][$key] = array_merge(
					$this->settings['collections'][$key],
					$this->data['settings']['collections'][$key]);
			}
		}

		$mysql_where = array();

		if(ee()->input->get_post( 'sample_table_json', false )) {
			$_save_data = json_decode(ee()->input->get_post( 'sample_table_json', false ));

			foreach ($_save_data as $key => $value) {
				$data = array();

				foreach ($value as $row => $row_value) {
					if ($row != 'entry_id') {
						$data[$row] = $row_value;
					}
				}

				ee()->db->where('entry_id', $value->entry_id);
				ee()->db->update('exp_mx_simple_tables_c_'.$data_settings['collection_id'], $data);
			}

		}

		if(ee()->input->get_post( 'filters', false )) {

			foreach (ee()->input->get_post( 'search') as $key => $value) {
				if (trim($value['text']) != '') {
					$condition =  $value['column'] . ' ';

					if ($value['condition'] == 0) {
						$condition .=	" = '" . $value['text'] . "'";
					}

					if ($value['condition'] == 1) {
						$condition .=	"LIKE '%".$value['text'] . "'";
					}

					if ($value['condition'] == 2) {
						$condition .=	"LIKE '". $value['text'] . "%'";
					}

					if ($value['condition'] == 3) {
						$condition .=	"LIKE '%". $value['text'] . "%'";
					}

					if ($value['condition'] == 4) {
						$condition .=	"NOT LIKE '%". $value['text'] . "%'";
					}

					$mysql_where[] = $condition;
				}
			}
		}

		if ( ee()->input->get_post( 'method' ) == 'collection' ) {
			if ( ee()->db->table_exists( 'exp_mx_simple_tables_c_'.$data_settings['collection_id'] ) ) {

				$offset = ee()->input->get_post( 'rownum', 0 );

				$offset .= ($offset != 0) ? ',' : '';

				$query_sql = "SELECT * FROM " . 'exp_mx_simple_tables_c_'.$data_settings['collection_id'] . (count($mysql_where) > 0 ? ' WHERE ' : '') . implode(" AND ", $mysql_where) . " LIMIT $offset $this->perpage" ;


				$query = ee()->db->query($query_sql);

				if ( $query->num_rows() !== 0 ) {

					$this->_load_assets();

					$this->data['settings']['entries'] = $query->result_array();

					ee()->load->library('pagination');
					$p_config = $this->pagination_config( 'collection', $this->data['settings']['collections'][$data_settings['collection_id']]['entries'],$data_settings['collection_id']);
					ee()->pagination->initialize($p_config);
    				$this->data['settings']['pagination'] = ee()->pagination->create_links();
				}
			}
		}

		$this->data['settings'] = array_merge($this->settings, $this->data['settings']);

		if ( $this->data['message'] = ee()->session->flashdata( 'msg' ) ) {
			ee()->javascript->output( array(
					'$.ee_notice("'.lang( $this->data['message'] ).'",{type:"success",open:true});',
					'window.setTimeout(function(){$.ee_notice.destroy()}, 2000);'
				) );
		}

		// Check if there's a group_id there
		$from_group = '';

		if ( $group_id = ee()->input->get( 'group_id' ) ) {
			$from_group = AMP.'from='.$group_id;
		}

		ee()->cp->set_right_nav( array(
				'collections'     => $this->base_url,
				'add_collections' => $this->base_url.AMP.'method=add_collections',
				'help'            => $this->base_url.AMP.'method=help'
			) );



		return ee()->load->view( $file, $this->data, TRUE );
	}

	/**
	 * Pagination Config
	 *
	 * @param [type]  $method     [description]
	 * @param [type]  $total_rows [description]
	 * @return [type]             [description]
	 */

	function pagination_config( $method, $total_rows, $collection_id ) {
		// Pass the relevant data to the paginate class
		$config['base_url']             = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=mx_simple_tables'.AMP.'method='.$method.AMP.'collection_id='.$collection_id;
		$config['total_rows']           = $total_rows;
		$config['per_page']             = $this->perpage;
		$config['page_query_string']    = TRUE;
		$config['query_string_segment'] = 'rownum';
		$config['full_tag_open']        = '<p id="paginationLinks">';
		$config['full_tag_close']       = '</p>';
		$config['prev_link']            = '<img src="'.ee()->cp->cp_theme_url.'images/pagination_prev_button.gif" width="13" height="13" alt="<" />';
		$config['next_link']            = '<img src="'.ee()->cp->cp_theme_url.'images/pagination_next_button.gif" width="13" height="13" alt=">" />';
		$config['first_link']           = '<img src="'.ee()->cp->cp_theme_url.'images/pagination_first_button.gif" width="13" height="13" alt="< <" />';
		$config['last_link']            = '<img src="'.ee()->cp->cp_theme_url.'images/pagination_last_button.gif" width="13" height="13" alt="> >" />';

		return $config;
	}


	// --------------------------------------------------------------------

	/**
	 * Load assets: extra JS and CSS
	 *
	 * @access     private
	 * @return     void
	 */
	private function _load_assets() {
		// -------------------------------------
		//  Define placeholder
		// -------------------------------------

		$header = $footer = array();

		// -------------------------------------
		//  Loop through assets
		// -------------------------------------

		$asset_url = ( ( defined( 'URL_THIRD_THEMES' ) )
			? URL_THIRD_THEMES
			: ee()->config->item( 'theme_folder_url' ) . 'third_party/' )
			. $this->package . '/';


		foreach ( $this->mcp_assets as $file ) {
			// location on server
			$file_url = $asset_url.$file;
			//.'?v='.MX_SIMPLE_TABLES_VERSION;

			if ( substr( $file, -3 ) == 'css' ) {
				$header[] = '<link charset="utf-8" type="text/css" href="'.$file_url.'" rel="stylesheet" media="screen" />';
			}
			elseif ( substr( $file, -2 ) == 'js' ) {
				$footer[] = '<script charset="utf-8" type="text/javascript" src="'.$file_url.'"></script>';
			}
		}

		// -------------------------------------
		//  Add combined assets to header/footer
		// -------------------------------------

		if ( $header ) ee()->cp->add_to_head( implode( NL, $header ) );
		if ( $footer ) ee()->cp->add_to_foot( implode( NL, $footer ) );
	}

	function content_wrapper( $content_view, $lang_key, $vars = array() ) {
		$vars['content_view'] = $content_view;
		$vars['_base']        = $this->base;
		$vars['_form_base']   = $this->form_base;
		$vars['img_path']     = ee()->config->item( 'theme_folder_url' );
		ee()->cp->set_variable( 'cp_page_title', lang( $lang_key ) );
		ee()->cp->set_breadcrumb( $this->base, lang( 'mx_simple_tables_module_name' ) );

		return ee()->load->view( '_wrapper', $vars, TRUE );
	}

}

/* End of file mcp.mx_simple_tables.php */
/* Location: ./system/expressionengine/third_party/mx_simple_tables/mcp.mx_simple_tables.php */
