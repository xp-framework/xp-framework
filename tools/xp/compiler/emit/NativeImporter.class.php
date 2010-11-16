<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Imports native functions. Implementations are constructed inside the 
   * emitter implementations.
   *
   * @see   xp://xp.compiler.emit.source.NativeImporter
   */
  abstract class NativeImporter extends Object {
    
    /**
     * Import all functions from a given extension
     *
     * @param   string extension
     * @param   string function
     * @return  array<var, var> import
     * @throws  lang.IllegalArgumentException if extension or function don't exist
     */
    public abstract function importAll($extension);
    
    /**
     * Import a single function
     *
     * @param   string extension
     * @param   string function
     * @return  array<var, var> import
     * @throws  lang.IllegalArgumentException if extension or function don't exist
     */
    public abstract function importSelected($extension, $function);
    
    /**
     * Check whether a given function exists
     *
     * @param   string extension
     * @param   string function
     * @return  array<var, var> import
     * @throws  lang.IllegalArgumentException if extension or function don't exist
     */
    public abstract function hasFunction($extension, $function);
    
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
