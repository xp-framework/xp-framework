<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase');

  /**
   * Test case variation
   *
   * @see   xp://unittest.TestCase
   */
  class TestVariation extends TestCase {
      
    /**
     * Constructor
     *
     * @param   unittest.TestCase base
     * @param   var[] args
     */
    public function __construct($base, $args) {
      $uniq= '';
      foreach ((array)$args as $arg) {
        $uniq.= '-'.xp::stringOf($arg);
      }
      parent::__construct($base->getName().$uniq);
    }
  }
?>