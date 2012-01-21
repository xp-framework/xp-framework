<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * (Insert class' description here)
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
