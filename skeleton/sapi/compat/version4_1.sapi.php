<?php
/* This file provides the CLI sapi for the XP framework
 * 
 * $Id$
 */

  // {{{ proto bool is_a (&object object, string class_name) 
  //     See php://is_a
  if (!function_exists('is_a')) { function is_a(&$object, $name) {
    return (
      (get_class($object) == strtolower($name)) ||
      (is_subclass_of($object, $name))
    );
  }}
  // }}}
  
  // {{{ proto mixed var_export (&mixed data [, bool return= 0])
  //     See php://var_export
  if (!function_exists('var_export')) { function var_export(&$data, $return= 0) {
    ob_start();
    var_dump($data);
    $dump= ob_get_contents();
    ob_end_clean();
    if ($return) return $dump;
    
    echo $dump;
  }}
  // }}}
  
  // {{{ proto array array_change_key_case(array a, int case) 
  //     See php://array_change_key_case
  if (!function_exists('array_change_key_case')) { 
    define('CASE_LOWER', 0);
    define('CASE_UPPER', 1);

    function array_change_key_case($a, $case) {
      $r= array();
      switch ($case) {
        case CASE_LOWER: foreach (array_keys($a) as $k) $r[strtolower($k)]= &$a[$k]; break;
        case CASE_UPPER: foreach (array_keys($a) as $k) $r[strtoupper($k)]= &$a[$k]; break;
        default: $r= FALSE;
      }
      return $r;
    }
  }
  // }}}
  
  // {{{ proto array sybase_fetch_assoc(resource result)
  //     See php://sybase_fetch_assoc
  if (!function_exists('sybase_fetch_assoc')) { function sybase_fetch_assoc($res) {
    if (is_array($r= sybase_fetch_array($res))) foreach (array_keys($r) as $k) {
      if (is_int($r[$k])) unset($r[$k]);
    }
    return $r;
  }}
  // }}}
?>
