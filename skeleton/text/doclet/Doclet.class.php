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
   *       while ($this->classes->hasNext()) {
   *         echo $this->classes->next()->qualifiedName(), "\n";
   *       }
   *     }
   *   }
   * </code>
   *
   * @see      http://java.sun.com/j2se/1.5.0/docs/guide/javadoc/
   * @purpose  Abstract base class
   */
  abstract class Doclet extends Object {
    public $options= array();
    public $classes= NULL;

    /**
     * Returns an option by a given name or the specified default value
     * if the option does not exist.
     *
     * @param   string name
     * @param   string default default NULL
     * @return  string
     */
    public function option($name, $default= NULL) {
      return isset($this->options[$name]) ? $this->options[$name] : $default;
    }
 
    /**
     * Generate documentation here.
     *
     * @param   text.doclet.RootDoc root
     * @return  var
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
      return new ClassIterator($classes, $root);
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
