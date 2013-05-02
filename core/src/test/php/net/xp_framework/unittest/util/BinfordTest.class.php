<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses('unittest.TestCase', 'util.Binford');

  /**
   * Test Binford class
   *
   * @see  xp://util.Binford
   */
  class BinfordTest extends TestCase {
    protected static $observable;

    /**
     * Tests constructor
     */
    #[@test]
    public function can_create() {
      new Binford(6100);
    }

    /**
     * Tests constructor
     */
    #[@test]
    public function default_power_is_6100() {
      $this->assertEquals(new Binford(6100), new Binford());
    }

    /**
     * Tests getPoweredBy()
     */
    #[@test]
    public function get_powered_by_returns_powerr() {
      $this->assertEquals(6100, create(new Binford(6100))->getPoweredBy());
    }

    /**
     * Tests setPoweredBy()
     */
    #[@test]
    public function set_powered_by_modifies_power() {
      $binford= new Binford(6100);
      $binford->setPoweredBy(61000);  // Hrhr, even more power!
      $this->assertEquals(61000, $binford->getPoweredBy());
    }

    /**
     * Tests setPoweredBy()
     */
    #[@test]
    public function zero_power_allowed() {
      new Binford(0);
    }

    /**
     * Tests setPoweredBy()
     */
    #[@test]
    public function fraction_0_61_power_allowed() {
      new Binford(0.61);
    }

    /**
     * Tests setPoweredBy()
     */
    #[@test]
    public function fraction_6_1_power_allowed() {
      new Binford(6.1);
    }

    /**
     * Tests setPoweredBy()
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function non_binford_number_not_allowed() {
      new Binford(6200);
    }

    /**
     * Tests setPoweredBy()
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function double_binford_number_not_allowed() {
      new Binford(6100 * 2);
    }

    /**
     * Tests toString()
     */
    #[@test]
    public function string_representation() {
      $this->assertEquals('util.Binford(6100)', create(new Binford(6100))->toString());
    }

    /**
     * Tests getHeader()
     */
    #[@test]
    public function header_representation() {
      $this->assertEquals(
        new Header('X-Binford', '6100 (more power)'),
        create(new Binford(6100))->getHeader()
      );
    }
  }
?>
