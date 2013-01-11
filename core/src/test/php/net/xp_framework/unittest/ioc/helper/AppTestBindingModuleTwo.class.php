<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('ioc.module.BindingModule');

  /**
   * Helper class for test cases.
   */
  class AppTestBindingModuleTwo extends Object implements BindingModule {
    /**
     * configure the binder
     *
     * @param  ioc.Binder  $binder
     */
    public function configure(Binder $binder) {
      $binder->bind('lang.Generic')
             ->toInstance(newinstance('lang.Object', array(), '{ public function get() { return "bar"; } }'));
    }
  }
?>
