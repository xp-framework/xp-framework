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
  class net·xp_lang·tests·syntax·php·StringTest extends net·xp_lang·tests·syntax·php·ParserTestCase {
  
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
