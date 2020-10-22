<?php
if (! defined('MX_SIMPLE_TABLES_PACKAGE'))
{
	define('MX_SIMPLE_TABLES_NAME', 'MX Simple Tables');
	define('MX_SIMPLE_TABLES_VERSION',  '0.7.2');
	define('MX_SIMPLE_TABLES_PACKAGE', 'mx_simple_tables');
	define('MX_SIMPLE_TABLES_AUTHOR',  'Max Lazar');
	define('MX_SIMPLE_TABLES_DOCS',  '');
	define('MX_SIMPLE_TABLES_DESC',  '');
	define('MX_SIMPLE_TABLES_DEBUG',    FALSE);

}

/**
 * < EE 2.6.0 backward compat
 */

if ( ! function_exists('ee'))
{
	function ee()
	{
		static $EE;
		if ( ! $EE) $EE = get_instance();
		return $EE;
	}
}


/* End of file config.php */
/* Location: ./system/expressionengine/third_party/mx_simple_tables/config.php */