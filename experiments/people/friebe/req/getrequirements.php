<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('text.PHPParser', 'lang.ElementNotFoundException');
  
  // {{{ public class FullName
  class FullName extends Object {

    // {{{ public static string searchFor(string filename) throws ElementNotFoundException
    function searchFor($filename) {
      static $includepaths= NULL;
      
      if (NULL === $includepaths) {
        $includepaths= explode(PATH_SEPARATOR, ini_get('include_path'));
      }
      foreach ($includepaths as $path) {
        $fn= $path.DIRECTORY_SEPARATOR.$filename;
        if (!is_file($fn)) continue;
        return realpath($fn);
      }
      
      return throw(new ElementNotFoundException('Could not find file "'.$filename.'"'));
    }
    // }}}
    
    // {{{ public static string forFile(string filename)
    function forFile($filename) {
      if (strstr($filename, DIRECTORY_SEPARATOR)) {     // Already fully qualified
        return realpath($filename);
      }
      return FullName::searchFor($filename);
    }
    // }}}
    
    // {{{ public static string forUses(string uses)
    function forUses($uses) {
      return FullName::searchFor(
        strtr($uses, '.', DIRECTORY_SEPARATOR).'.class.php'
      );
    }
    // }}}
    
    // {{{ public static string forSapi(string sapi)
    function forSapi($sapi) {
      return FullName::searchFor(
        'sapi'.DIRECTORY_SEPARATOR.strtr($sapi, '.', DIRECTORY_SEPARATOR).'.sapi.php'
      );
    }
    // }}}

  }
  // }}}
  
  // {{{ public class Requirements
  class Requirements extends Object {
    var
      $file = '',
      $seen = array(),
      $deps = array();
      
    // {{{ private __construct(string filename)
    function __construct($filename, $seen= array()) {
      $this->file= FullName::forFile($filename);
      $this->seen= $seen;
    }
    // }}}
      
    // {{{ public string add(string fullname)
    function add($fullname) {

      // Check if we've already been here
      if (isset($this->seen[$fullname])) return;
      $this->seen[$fullname]= 1;
      
      try(); {
        $r= &Requirements::forFile($fullname, $this->seen);
      } if (catch('Exception', $e)) {
        return throw($e);
      }

      $this->deps[]= $fullname;
      $this->deps= array_unique(array_merge($this->deps, $r->deps));
      return $fullname;
    }
    // }}}

    // {{{ public static Requirements forFile(string filename, string[] list) throws Exception   
    function forFile($filename, $list) {
      if (is('null', $filename)) return;    // NullPointer

      $requirements= &new Requirements($filename, $list);
      $idx= basename($filename);
      try(); {
        $parser= &new PHPParser($filename);
        $parser->parse();
      } if (catch('Exception', $e)) {
        return throw($e);
      }

      defined('DEBUG') && Console::writeLine($parser->toString());

      // First, go through the requirements
      foreach ($parser->requires as $required) {
        try(); {
          $fn= $requirements->add(FullName::forFile($required));
        } if (catch('ElementNotFoundException', $e)) {
          if (!defined('IGNORE_NOTFOUND')) return throw($e);
          $e->printStackTrace();
          continue;
        }
        defined('VERBOSE') && Console::writeLine('+ ', $idx, ' requires file ', $required, ' (', $fn, ')');
      }

      // Second, the SAPIs
      foreach ($parser->sapis as $sapi) {
        try(); {
          $fn= $requirements->add(FullName::forSapi($sapi));
        } if (catch('ElementNotFoundException', $e)) {
          if (!defined('IGNORE_NOTFOUND')) return throw($e);
          $e->printStackTrace();
          continue;
        }
        defined('VERBOSE') && Console::writeLine('+ ', $idx, ' has SAPI ', $sapi, ' (', $fn, ')');
      }

      // Then check all of the classes in uses()
      foreach ($parser->uses as $uses) {
        try(); {
          $fn= $requirements->add(FullName::forUses($uses));
        } if (catch('ElementNotFoundException', $e)) {
          if (!defined('IGNORE_NOTFOUND')) return throw($e);
          $e->printStackTrace();
          continue;
        }
        defined('VERBOSE') && Console::writeLine('+ ', $idx, ' uses class ', $uses, ' (', $fn, ')');
      }

      // If file required itself, we have some sort of circular 
      // dependency - resolve if
      if (FALSE !== ($pos= array_search($requirements->file, $requirements->deps))) {
        defined('VERBOSE') && Console::writeLine('! Circular dependency detected for ', $idx, ', resolving');
        unset($requirements->deps[$pos]);
      }
      return $requirements;
    }
    // }}}

  }
  // }}}
  
  // {{{ main
  $param= &new ParamString();
  if ($param->exists('verbose')) define('VERBOSE', 1);
  if ($param->exists('debug')) define('DEBUG', 1);
  if ($param->exists('force')) define('IGNORE_NOTFOUND', 1);
  
  try(); {
    $requirements= &Requirements::forFile($param->value(1));
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  defined('DEBUG') && Console::writeLine($requirements->toString());
  
  Console::writeLine($requirements->file);
  for ($i= 0, $s= sizeof($requirements->deps); $i < $s; $i++) {
    Console::writeLine($requirements->deps[$i]);
  }
  // }}}
?>
