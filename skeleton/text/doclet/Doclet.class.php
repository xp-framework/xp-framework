<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('text.doclet.RootDoc');

  /**
   * This is the starting class for a doclet.
   *
   * Example:
   * <code>
   *   class NameListDoclet extends Doclet {
   * 
   *     public function start(RootDoc $root) {
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
  abstract class Doclet extends Object {
 
    /**
     * Generate documentation here.
     *
     * @param   text.doclet.RootDoc root
     * @return  bool TRUE on success
     */ 
    public abstract function start(RootDoc $root);

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
