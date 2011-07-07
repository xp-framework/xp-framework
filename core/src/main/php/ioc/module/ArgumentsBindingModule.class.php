<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('ioc.module.BindingModule');

  /**
   * @purpose  Binding module to configure the binder with arguments.
   */
  class ArgumentsBindingModule extends Object implements BindingModule {
    protected $argv;

    /**
     * constructor
     *
     * @param  string[]  $argv
     */
    public function __construct($argv) {
      $this->argv = $argv;
    }

    /**
     * configure the binder
     *
     * @param  ioc.Binder  $binder
     */
    public function configure(Binder $binder) {
      foreach ($this->argv as $position => $value) {
        $binder->bindConstant()
               ->named('argv.' . $position)
               ->to($value);
      }
    }
  }
?>