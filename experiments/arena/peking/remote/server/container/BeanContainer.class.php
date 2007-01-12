<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.Collection', 'util.log.Traceable');

  /**
   * Bean container
   *
   * @purpose  abstract baseclass
   */
  abstract class BeanContainer extends Object implements Traceable {
    public
      $instancePool = NULL;
    
    protected
      $cat  = NULL;

    /**
     * Set trace
     *
     * @param   util.log.LogCategory cat
     */
    public function setTrace($cat) {
      $this->cat= $cat;
    }

    /**
     * Get instance for class
     *
     * @param   lang.XPClass class
     * @return  remote.server.BeanContainer
     */
    public static function forClass($class) {
      $bc= new BeanContainer();
      $bc->instancePool= Collection::forClass($class->getName());
      return $bc;
    }

    /**
     * Invoke a method
     *
     * @param   lang.Object proxy
     * @param   string method
     * @param   mixed args
     * @return  mixed
     */
    public abstract function invoke($proxy, $method, $args);
  }
?>
