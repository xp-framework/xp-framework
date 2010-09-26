<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_lang.tests.syntax.xp';

  uses('net.xp_lang.tests.syntax.xp.ParserTestCase');

  /**
   * TestCase
   *
   */
  class net·xp_lang·tests·syntax·xp·StringTest extends ParserTestCase {
  
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
