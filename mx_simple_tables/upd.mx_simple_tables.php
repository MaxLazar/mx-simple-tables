<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// include base class
if ( ! class_exists('Mx_simple_tables_base'))
{
	require_once(PATH_THIRD.'mx_simple_tables/base.mx_simple_tables.php');
}
	/**
	 * -
	 * @package		MX 2 Step Authentication
	 * @subpackage	ThirdParty
	 * @category	Modules
	 * @author    Max Lazar <max@eec.ms>
	 * @copyright Copyright (c) 2014 Max Lazar (http://eec.ms)
	 * @link		http://eec.ms/
	 */
class Mx_simple_tables_upd extends Mx_simple_tables_base {

	/**
	 * Extension hooks
	 *
	 * @var        array
	 * @access     private
	 */
	private $hooks = array(
	);


	/**
	 * Constructor
	 *
	 * @access     public
	 * @return     void
	 */
	public function __construct()
	{
		// Call parent constructor
		parent::__construct();

		// Set class name
		$this->class_name = ucfirst(MX_SIMPLE_TABLES_PACKAGE);
	}

    /**
     * Installer for the Mx_simple_tables module
     */
    function install()
	{

		$data = array(
			'module_name' 	 => $this->class_name,
			'module_version' => MX_SIMPLE_TABLES_VERSION,
			'has_cp_backend' => 'y'
		);

		ee()->db->insert('modules', $data);

		if (!ee()->db->field_exists('settings', 'exp_modules')) {
			ee()->load->dbforge();
			$column = array('settings'	 => array('type' => 'TEXT'));
			ee()->dbforge->add_column('exp_modules', $column);
		}

		// --------------------------------------
		// Add row to modules table
		// --------------------------------------

		foreach ($this->hooks AS $hook)
		{
			$this->_add_hook($hook);
		}

		return TRUE;
	}


	/**
	 * Uninstall the Mx_simple_tables module
	 */
	function uninstall()
	{

		ee()->db->select('module_id');
		$query = ee()->db->get_where('modules', array('module_name' => $this->class_name));

		ee()->db->where('module_id', $query->row('module_id'));
		ee()->db->delete('module_member_groups');

		ee()->db->where('module_name', $this->class_name);
		ee()->db->delete('modules');

		ee()->db->where('class',$this->class_name);
		ee()->db->delete('actions');

		ee()->db->where('class', $this->class_name.'_mcp');
		ee()->db->delete('actions');

		//DELETE TIME-TABLE
		ee()->load->dbforge();
		ee()->dbforge->drop_table('exp_mx_simple_tables_board');


		return TRUE;
	}

	/**
	 * Update the Mx_simple_tables module
	 *
	 * @param $current current version number
	 * @return boolean indicating whether or not the module was updated
	 */

	function update($current = '')
	{
		return true;
	}

    	/**
	 * Add extension hook
	 *
	 * @access     private
	 * @param      string
	 * @return     void
	 */
	private function _add_hook($name)
	{
		ee()->db->insert('extensions',
			array(
				'class'    => $this->class_name.'_ext',
				'method'   => $name,
				'hook'     => $name,
				'settings' => serialize($this->settings),
				'priority' => 2,
				'version'  => $this->version,
				'enabled'  => 'y'
			)
		);
	}

}

/* End of file upd.mx_simple_tables.php */
/* Location: ./system/expressionengine/third_party/mx_simple_tables/upd.mx_simple_tables.php */