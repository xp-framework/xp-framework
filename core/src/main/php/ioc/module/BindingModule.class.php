<?php
/* This class is part of the XP framework
 *
 * $Id: BindingModule.class.php 2991 2011-02-12 23:35:48Z mikey $
 */

  uses('ioc.Binder');

  /**
   * @purpose  Interface for modules which configure the binder.
   */
  interface BindingModule {

    /**
     * configure the binder
     *
     * @param  ioc.Binder  $binder
     */
    public function configure(Binder $binder);
  }
?>