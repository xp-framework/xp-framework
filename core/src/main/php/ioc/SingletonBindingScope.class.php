<?php
/* This class is part of the XP framework
 *
 * $Id: SingletonBindingScope.class.php 2991 2011-02-12 23:35:48Z mikey $
 */

  uses('ioc.BindingScope');

  /**
   * @purpose  Scope which ensures always the same instance for a class is provided.
   */
  class SingletonBindingScope extends Object implements BindingScope {
    protected $instances = array();

    /**
     * returns the requested instance from the scope
     *
     * @param   lang.XPClass  $type  type of the object
     * @param   lang.XPClass  $impl  concrete implementation
     * @param   ioc.InjectionProvider  $provider
     * @return  object
     */
    public function getInstance(XPClass $type, XPClass $impl, InjectionProvider $provider) {
      $key = $impl->getName();
      if (!isset($this->instances[$key])) {
        $this->instances[$key] = $provider->get();
      }

      return $this->instances[$key];
    }
  }
?>