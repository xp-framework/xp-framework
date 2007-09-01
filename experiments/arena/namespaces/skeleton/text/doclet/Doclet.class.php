<?php
/* This class is part of the XP framework
 *
 * $Id: Doclet.class.php 9104 2007-01-03 17:13:06Z friebe $ 
 */

  namespace text::doclet;

  uses('text.doclet.RootDoc');

  /**
   * This is the starting class for a doclet.
   *
   * Example:
   * <code>
   *   class NameListDoclet extends Doclet {
   * 
   *     function start($root) {
   *       while ($root->classes->hasNext()) {
   *         echo $root->classes->next()->qualifiedName(), "\n";
   *       }
   *     }
   *   }
   * </code>
   *
   * @see      http://java.sun.com/j2se/1.5.0/docs/guide/javadoc/
   * @purpose  Abstract base class
   */
  class Doclet extends lang::Object {
 
    /**
     * Generate documentation here.
     *
     * @param   text.doclet.RootDoc root
     * @return  bool TRUE on success
     */ 
    public function start($root) {
      return TRUE;
    }

    /**
     * Get class iterator
     *
     * @param   text.doclet.RootDoc root
     * @param   string[] classnames passed via parameters
     * @return  text.doclet.ClassIterator
     * @throws  lang.XPException in case the iterator cannot be created.
     */ 
    public function iteratorFor($root, $classes) {
      $iterator= new ClassIterator($classes);
      $iterator->root= $root;
      return $iterator;
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
     * @return  array
     */
    public function validOptions() {
      return array();
    }
  }
?>
