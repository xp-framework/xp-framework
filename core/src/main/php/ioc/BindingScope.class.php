<?php
/* This class is part of the XP framework
 *
 * $Id: BindingScope.class.php 2991 2011-02-12 23:35:48Z mikey $
 */

  uses('ioc.InjectionProvider');

  /**
   * Interface for for different scopes of a binding.
   */
  interface BindingScope {

    /**
     * returns the requested instance from the scope
     *
     * @param   lang.XPClass  $type  type of the object
     * @param   lang.XPClass  $impl  concrete implementation
     * @param   ioc.InjectionProvider  $provider
     * @return  object
     */
    public function getInstance(XPClass $type, XPClass $impl, InjectionProvider $provider);
  }
?>