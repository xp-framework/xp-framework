<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_framework.unittest.core.generics';

  /**
   * Nullable value
   *
   */
  #[@generic(self= 'T')]
  class net·xp_framework·unittest·core·generics·Nullable extends Object {
    protected $value;

    /**
     * Constructor
     *
     * @param   T value
     */
    #[@generic(params= 'T')]
    public function __construct($value= NULL) {
      $this->value= $value;
    }

    /**
     * Returns whether a value exists
     *
     * @return  bool
     */
    public function hasValue() {
      return $this->value !== NULL;
    }

    /**
     * Sets value
     *
     * @param   T value
     * @return  self this instance
     */
    #[@generic(params= 'T')]
    public function set($value= NULL) {
      $this->value= $value;
      return $this;
    }

    /**
     * Returns value
     *
     * @return  T value
     */
    #[@generic(return= 'T')]
    public function get() {
      return $this->value;
    }
  }
?>
