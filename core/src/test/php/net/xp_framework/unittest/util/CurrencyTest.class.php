<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase', 'util.Currency');

  /**
   * TestCase
   *
   * @see      xp://util.Currency
   */
  class CurrencyTest extends TestCase {
  
    /**
     * Test getInstance() method
     */
    #[@test]
    public function get_instance_usd() {
      $this->assertEquals(Currency::$USD, Currency::getInstance('USD'));
    }

    /**
     * Test getInstance() method
     */
    #[@test]
    public function get_instance_eur() {
      $this->assertEquals(Currency::$EUR, Currency::getInstance('EUR'));
    }

    /**
     * Test getInstance() method
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function get_instance_nonexistant() {
      Currency::getInstance('@@not-a-currency@@');
    }
  }
?>
