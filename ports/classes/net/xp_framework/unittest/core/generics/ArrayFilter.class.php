<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_framework.unittest.core.generics';

  /**
   * Generic array filter
   *
   * Example:
   * <code>
   *   // Set up filter as anonymous class
   *   $webmethods= create('new ArrayFilter<Method>', array(), '{
   *     protected function accept($method) {
   *       return $method->hasAnnotation("webmethod");
   *     }
   *   }');
   *
   *   // Iterate over filtered array
   *   foreach ($webmethods->filter($class->getMethods()) as $method) {
   *     Console::writeLine("Webmethod: ", $method);
   *   }
   * </code>
   */
  #[@generic(self= 'T')]
  abstract class net·xp_framework·unittest·core·generics·ArrayFilter extends Object {
    
    /**
     * Accept method - called for each element in the specified list.
     * Return TRUE if the passed element should be included in the
     * filtered list, FALSE otherwise
     *
     * @param   T element
     * @return  bool
     */
    #[@generic(params= 'T')]
    protected abstract function accept($element);

    /**
     * Filter a list of elements
     *
     * @param   T[] elements
     * @return  T[] filtered
     */
    #[@generic(params= 'T[]', return= 'T[]')]
    public function filter($elements) {
      $filtered= array();
      foreach ($elements as $element) {
        if ($this->accept($element)) $filtered[]= $element;
      }
      return $filtered;
    }
  }
?>
