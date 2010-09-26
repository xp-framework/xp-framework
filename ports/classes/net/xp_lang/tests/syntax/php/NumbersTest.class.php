<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_lang.tests.syntax.php';

  uses('net.xp_lang.tests.syntax.php.ParserTestCase');

  /**
   * TestCase
   *
   */
  class net暖p_lang暗ests新yntax搆hp意umbersTest extends net暖p_lang暗ests新yntax搆hp感arserTestCase {
  
    /**
     * Test "1.a" raises a parser exception
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function illegalDecimalCharAfterDot() {
      $this->parse('1.a');
    }

    /**
     * Test "1.-" raises a parser exception
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function illegalDecimalMinusAfterDot() {
      $this->parse('0.-');
    }

    /**
     * Test "0xZ" raises a parser exception
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function illegalHexZ() {
      $this->parse('0xZ');
    }

    /**
     * Test "0x" raises a parser exception
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function illegalHexMissingAfterX() {
      $this->parse('0x');
    }
  }
?>
