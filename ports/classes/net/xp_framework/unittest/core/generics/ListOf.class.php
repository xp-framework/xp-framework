<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_framework.unittest.core.generics';

  /**
   * List of elements
   *
   */
  #[@generic(self= 'T')]
  class net·xp_framework·unittest·core·generics·ListOf extends Object {
    public $elements= array();

    /**
     * Constructor
     *
     * @param   T... initial
     */
    #[@generic(params= 'T...')]
    public function __construct() {
      $this->elements= func_get_args();
    }

    /**
     * Adds an element
     *
     * @param   T... elements
     * @return  net.xp_framework.unittest.core.generics.List self
     */
    #[@generic(params= 'T...')]
    public function withAll() {
      $this->elements= array_merge($this->elements, func_get_args());
      return $this;
    }

    /**
     * Returns a list of all elements
     *
     * @return  T[] elements
     */
    #[@generic(return= 'T[]')]
    public function elements() {
      return $this->elements;
    }
  }
?>
