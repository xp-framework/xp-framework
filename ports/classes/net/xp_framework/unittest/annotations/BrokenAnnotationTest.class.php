<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase');

  /**
   * Tests the XP Framework's annotations
   *
   * @see      rfc://0185
   */
  class BrokenAnnotationTest extends TestCase {
    
    /**
     * Get annotations
     *
     * @param   string class
     */
    protected function assertBrokenAnnotations($class, $message) {
      try {
        $a= Package::forName('net.xp_framework.unittest.annotations')->loadClass($class)->getAnnotations();
        $this->fail('No exception raised', $a, 'lang.ClassFormatException');
      } catch (ClassFormatException $e) {
        $this->assertEquals($message, substr($e->getMessage(), 0, strlen($message)), $e->getMessage());
      }
    }

    /**
     * Tests missing ending "]"
     *
     */
    #[@test]
    public function noEndingBracket() {
      $this->assertBrokenAnnotations('NoEndingBracket', 'Unterminated annotation');
    }

    /**
     * Tests missing ending ")" in key/value pairs
     *
     */
    #[@test]
    public function unmatchedBracket() {
      $this->assertBrokenAnnotations('UnmatchedBracket', 'Parse error');
    }

    /**
     * Tests unterminated string literal
     *
     */
    #[@test]
    public function unterminatedStringLiteral() {
      $this->assertBrokenAnnotations('UnterminatedStringLiteral', 'Parse error');
    }
  }
?>
