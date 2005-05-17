<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('RootDoc');

  /**
   * This is the starting class for a doclet.
   *
   * Example:
   * <code>
   *   class NameListDoclet extends Doclet {
   * 
   *     function start(&$root) {
   *       while ($root->classes->hasNext()) {
   *         $classdoc= &$root->classes->next();
   *         echo $classdoc->qualifiedName(), "\n";
   *       }
   *     }
   *   }
   * </code>
   *
   * @see      http://java.sun.com/j2se/1.5.0/docs/guide/javadoc/
   * @purpose  Abstract base class
   */
  class Doclet extends Object {
 
    /**
     * Generate documentation here.
     *
     * @access  public
     * @param   &RootDoc root
     * @return  bool TRUE on success
     */ 
    function start(&$root) {
      return TRUE;
    }
    
    /**
     * Return a list of valid options as an associative array, keys
     * forming parameter names and values defining whether this option
     * expects a value.
     *
     * Example:
     * <code>
     *   return array(
     *     'classpath' => HAS_VALUE,
     *     'verbose'   => OPTION_ONLY
     *   );
     * </code>
     *
     * Returns an empty array in this default implementation.
     *
     * @access  public
     * @return  array
     */
    function validOptions() {
      return array();
    }
  }
?>
