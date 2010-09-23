<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_lang.tests.syntax.xp.ParserTestCase');

  /**
   * TestCase
   *
   */
  class StringTest extends ParserTestCase {
  
    /**
     * Test unterminated string
     *
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function unterminatedString() {
      $this->parse("'Hello World");
    }
  }
?>
