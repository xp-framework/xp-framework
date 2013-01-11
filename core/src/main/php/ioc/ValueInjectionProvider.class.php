<?php
/* This class is part of the XP framework
 *
 * $Id: ValueInjectionProvider.class.php 2991 2011-02-12 23:35:48Z mikey $
 */

  uses('ioc.InjectionProvider');

  /**
   * Simple injection provider for a single predefined value.
   */
  class ValueInjectionProvider extends Object implements InjectionProvider {
    protected $value;

    /**
     * constructor
     *
     * @param  mixed  $value  value to provide
     */
    public function __construct($value) {
      $this->value = $value;
    }

    /**
     * returns the value to provide
     *
     * @param   string  $name  optional
     * @return  mixed
     */
    public function get($name = NULL) {
      return $this->value;
    }
  }
?>