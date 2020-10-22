<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MX 2 Step Authentication helper functions
 *
 * @package  MX 2 Step Authentication
 * @subpackage ThirdParty
 * @category Modules
 * @author    Max Lazar <max@eec.ms>
 * @copyright Copyright (c) 2014 Max Lazar (http://www.eec.ms)
 * @link  http://www.eec.ms/
 */

/**
 * Encode array to string
 *
 * @param      array     Array to encode
 * @return     string
 */
if ( ! function_exists('mx_array_encode'))
{
	function mx_array_encode($array = array())
	{
		return str_replace('/', '_', rtrim(base64_encode(serialize($array)), '='));
	}
}

// --------------------------------------------------------------------

/**
 * Decode string back to array
 *
 * @param      string    String to decode
 * @return     array
 */
if ( ! function_exists('mx_array_decode'))
{
	function mx_array_decode($str = '')
	{
		return (is_string($str) && strlen($str)) ? @unserialize(base64_decode(str_replace('_', '/', $str))) : FALSE;
	}
}

// --------------------------------------------------------------------

/**
* Encode settings for DB
*
* @param      array     Array to encode
* @return     string
*/
if ( ! function_exists('mx_encode_settings'))
{
	function mx_encode_settings($array = array())
	{
		return base64_encode(serialize($array));
	}
}


// --------------------------------------------------------------------

/**
 * Linearize array
 *
 * @param      array
 * @param      string
 * @return     string
 */
if ( ! function_exists('mx_linearize'))
{
	function mx_linearize($array = array(), $d = '|')
	{
		return (string) ($array ? $d.implode($d, $array).$d : '');
	}
}

/**
 * Delinearize string
 *
 * @param      string
 * @return     array
 */
if ( ! function_exists('mx_delinearize'))
{
	function mx_delinearize($string = '', $d = '|')
	{
		return (array) array_filter(explode($d, trim($string, $d)));
	}
}


if ( ! function_exists('mx_multiselect_size'))
{
	function mx_multiselect_size($size, $max = 10)
	{
		return sprintf(' size="%s"', ($size > $max ? $max : $size));
	}
}


// --------------------------------------------------------------------

/**
 * Flatten results
 *
 * Given a DB result set, this will return an (associative) array
 * based on the keys given
 *
 * @param      array
 * @param      string    key of array to use as value
 * @param      string    key of array to use as key (optional)
 * @return     array
 */
if ( ! function_exists('mx_flatten_results'))
{
	function mx_flatten_results($resultset, $val, $key = FALSE)
	{
		$array = array();

		foreach ($resultset AS $row)
		{
			if ($key !== FALSE)
			{
				$array[$row[$key]] = $row[$val];
			}
			else
			{
				$array[] = $row[$val];
			}
		}

		return $array;
	}
}

// --------------------------------------------------------------------

/**
 * Associate results
 *
 * Given a DB result set, this will return an (associative) array
 * based on the keys given
 *
 * @param      array
 * @param      string    key of array to use as key
 * @param      bool      sort by key or not
 * @return     array
 */
if ( ! function_exists('mx_associate_results'))
{
	function mx_associate_results($resultset, $key, $sort = FALSE)
	{
		$array = array();

		foreach ($resultset AS $row)
		{
			if (array_key_exists($key, $row) && ! array_key_exists($row[$key], $array))
			{
				$array[$row[$key]] = $row;
			}
		}

		if ($sort === TRUE)
		{
			ksort($array);
		}

		return $array;
	}
}

// --------------------------------------------------------------

/**
 * Get cache value, either using the cache method (EE2.2+) or directly from cache array
 *
 * @param       string
 * @param       string
 * @return      mixed
 */
if ( ! function_exists('mx_get_cache'))
{
	function mx_get_cache($a, $b)
	{
		if (method_exists(ee()->session, 'cache'))
		{
			return ee()->session->cache($a, $b);
		}
		else
		{
			return (isset(ee()->session->cache[$a][$b]) ? ee()->session->cache[$a][$b] : FALSE);
		}
	}
}

// --------------------------------------------------------------

/**
 * Set cache value, either using the set_cache method (EE2.2+) or directly to cache array
 *
 * @param       string
 * @param       string
 * @param       mixed
 * @return      void
 */
if ( ! function_exists('mx_set_cache'))
{
	function mx_set_cache($a, $b, $c)
	{
		if (method_exists(ee()->session, 'set_cache'))
		{
			ee()->session->set_cache($a, $b, $c);
		}
		else
		{
			ee()->session->cache[$a][$b] = $c;
		}
	}
}


// --------------------------------------------------------------

/**
 * Debug
 *
 * @param       mixed
 * @param       bool
 * @return      void
 */
if ( ! function_exists('mx_dump'))
{
	function mx_dump($var, $exit = TRUE)
	{
		echo '<pre>'.print_r($var, TRUE).'</pre>';
		if ($exit) exit;
	}
}

// --------------------------------------------------------------

/* End of file mx_schedule.php */
