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
        return $filename;
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
      $files= array();
      
    // {{{ public string add(string filename)
    function add($fullname) {
      try(); {
        $r= &Requirements::forFile($fullname);
      } if (catch('Exception', $e)) {
        return throw($e);
      }

      $this->files[]= $fullname;
      $this->files= array_unique(array_merge($this->files, $r->files));
      return $fullname;
    }
    // }}}

    // {{{ public static Requirements forFile(string filename) throws Exception   
    function forFile($filename) {
      if (is('null', $filename)) return;    // NullPointer

      $requirements= &new Requirements();
      $idx= basename($filename);
      try(); {
        $parser= &new PHPParser($filename);
        $parser->parse();
      } if (catch('Exception', $e)) {
        return throw($e);
      }

      defined('VERBOSE') && Console::writeLine($parser->toString());

      // First, go through the requirements
      foreach ($parser->requires as $required) {
        try(); {
          $fn= $requirements->add(FullName::forFile($required));
        } if (catch('ElementNotFoundException', $e)) {
          if (!defined('IGNORE_NOTFOUND')) return throw($e);
          $e->printStackTrace();
          continue;
        }
        Console::writeLine('+ ', $idx, ' requires file ', $required, ' (', $fn, ')');
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
        Console::writeLine('+ ', $idx, ' has SAPI ', $sapi, ' (', $fn, ')');
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
        Console::writeLine('+ ', $idx, ' uses class ', $uses, ' (', $fn, ')');
      }

      return $requirements;
    }
    // }}}

  }
  // }}}
  
  // {{{ main
  $param= &new ParamString();
  if ($param->exists('verbose')) define('VERBOSE', 1);
  if ($param->exists('force')) define('IGNORE_NOTFOUND', 1);
  
  try(); {
    $requirements= &Requirements::forFile($param->value(1));
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  Console::writeLine($requirements->toString());
  // }}}
?>
