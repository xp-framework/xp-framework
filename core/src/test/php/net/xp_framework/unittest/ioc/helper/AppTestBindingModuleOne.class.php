<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('ioc.module.BindingModule');

  /**
   * Helper class for test cases.
   */
  class AppTestBindingModuleOne extends Object implements BindingModule {
    /**
     * configure the binder
     *
     * @param  ioc.Binder  $binder
     */
    public function configure(Binder $binder) {
      $binder->bind('lang.Object')
             ->toInstance(newinstance('lang.Object', array(), '{ public function get() { return "foo"; } }'));
    }
  }
?>
