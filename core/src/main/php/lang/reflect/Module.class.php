<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * A module represents a named class path.
   *
   */
  class Module extends Object {
    protected $reflect;

    /**
     * Creates a new instance of a module with a given name.
     *
     * @param   string name
     */
    protected function __construct($name) {
      $this->reflect= xp::$registry['modules'][$name];
    }
    
    /**
     * Returns module name
     *
     * @return  string
     */
    public function getName() {
      return $this->reflect[1];
    }

    /**
     * Returns comment
     *
     * @return  string
     */
    public function getComment() {
      return $this->reflect[0]->getComment();
    }

    /**
     * Check whether an annotation exists
     *
     * @param   string name
     * @param   string key default NULL
     * @return  bool
     */
    public function hasAnnotation($name, $key= NULL) {
      return $this->reflect[0]->hasAnnotation($name, $key);
    }

    /**
     * Retrieve annotation by name
     *
     * @param   string name
     * @param   string key default NULL
     * @return  var
     * @throws  lang.ElementNotFoundException
     */
    public function getAnnotation($name, $key= NULL) {
      return $this->reflect[0]->getAnnotation($name, $key);
    }

    /**
     * Retrieve whether this module has annotations
     *
     * @return  bool
     */
    public function hasAnnotations() {
      return $this->reflect[0]->hasAnnotations();
    }

    /**
     * Retrieve all of a module's annotations
     *
     * @return  var[] annotations
     */
    public function getAnnotations() {
      return $this->reflect[0]->getAnnotations();
    }

    /**
     * Gets a module by a given name
     *
     * @param   string name
     * @return  lang.reflect.Module name
     * @throws  lang.ElementNotFoundException if the module doesn't exist
     */
    public static function forName($name) {
      if (!isset(xp::$registry['modules'][$name])) {
        raise('lang.ElementNotFoundException', 'No such module '.$name);
      }
      
      return new self($name);
    }
  }
?>
