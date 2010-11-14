<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'xp.compiler.emit.source';
  
  uses('xp.compiler.emit.NativeImporter');

  /**
   * Imports native functions from PHP.
   *
   * @test    xp://net.xp_lang.tests.SourceNativeImporterTest
   */
  class xp·compiler·emit·source·NativeImporter extends NativeImporter {
    protected static $coreExtAvailable= FALSE;
    
    static function __static() {
      self::$coreExtAvailable= extension_loaded('core');
    }
    
    /**
     * Import all functions from a given extension
     *
     * @param   string extension
     * @param   string function
     * @return  array<var, var> import
     * @throws  lang.IllegalArgumentException if extension or function don't exist
     */
    public function importAll($extension) {
      try {
        $e= new ReflectionExtension($extension);
      } catch (ReflectionException $e) {
        throw new IllegalArgumentException('Extension '.$extension.' does not exist');
      }
      return array(0 => array($extension => TRUE));
    }
    
    /**
     * Returns a PHP function's extension
     *
     * @param   php.ReflectionFunction func
     * @return  php.ReflectionExtension
     */
    protected function functionsExtension($func) {
      if (method_exists($func, 'getExtension')) return $func->getExtension();
      
      // BC with older PHP versions
      foreach (get_loaded_extensions() as $name) {
        $r= new ReflectionExtension($name);
        $f= $r->getFunctions(); 
        if (isset($f[$func->getName()])) return $r;
      }
      return NULL;
    }

    /**
     * Import a single function
     *
     * @param   string extension
     * @param   string function
     * @return  array<var, var> import
     * @throws  lang.IllegalArgumentException if extension or function don't exist
     */
    public function importSelected($extension, $function) {
      if ('core' === $extension && !self::$coreExtAvailable) {
        $e= NULL;
      } else {
        try {
          $e= new ReflectionExtension($extension);
        } catch (ReflectionException $e) {
          throw new IllegalArgumentException('Extension '.$extension.' does not exist');
        }
      }
      try {
        $f= new ReflectionFunction($function);
      } catch (ReflectionException $e) {
        throw new IllegalArgumentException('Function '.$function.' does not exist');
      }
      if ($e != ($fe= $this->functionsExtension($f))) {
        throw new IllegalArgumentException('Function '.$function.' is not inside extension '.$extension.' (but '.($fe ? $fe->getName() : '(n/a)').')');
      }
      return array($function => TRUE);
    }

    /**
     * Check whether a given function exists
     *
     * @param   string extension
     * @param   string function
     * @return  array<var, var> import
     * @throws  lang.IllegalArgumentException if extension or function don't exist
     */
    public function hasFunction($extension, $function) {
      if ('core' === $extension && !self::$coreExtAvailable) {
        return function_exists($function);
      } else if ($list= get_extension_funcs($extension)) {
        return in_array($function, $list);
      } else {
        return FALSE;
      }
    }
    
    /**
     * Import a given pattern
     *
     * Specific:
     * <code>
     *   import native standard.array_keys;
     *   import native pcre.preg_match;
     *   import native core.strlen;
     * </code>
     *
     * On-Demand:
     * <code>
     *   import native standard.*;
     * </code>
     *
     * @param   string pattern
     * @return  array<var, var> import
     * @throws  lang.IllegalArgumentException in case errors occur during importing
     * @throws  lang.FormatException in case the input string is malformed
     */
    public function import($pattern) {
      $p= strrpos($pattern, '.');
      if ($p <= 0) {
        throw new FormatException('Malformed import <'.$pattern.'>');
      } else  if ('.*' == substr($pattern, -2)) {
        return $this->importAll(substr($pattern, 0, $p));
      } else {
        return $this->importSelected(substr($pattern, 0, $p), substr($pattern, $p+ 1));
      }
    }
  }
?>
