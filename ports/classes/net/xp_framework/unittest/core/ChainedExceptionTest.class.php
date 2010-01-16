<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'unittest.TestCase',
    'lang.ChainedException'
  );

  /**
   * Test ChainedException class
   *
   * @see      xp://util.ChainedException
   * @purpose  Unit Test
   */
  class ChainedExceptionTest extends TestCase {

    /**
     * Tests a ChainedException without a cause
     *
     */
    #[@test]
    public function withoutCause() {
      $e= new ChainedException('Message', NULL);
      $this->assertEquals('Message', $e->getMessage()) &&
      $this->assertNull($e->getCause()) &&
      $this->assertFalse(strstr($e->toString(), 'Caused by'));
    }

    /**
     * Tests a ChainedException with a cause
     *
     */
    #[@test]
    public function withCause() {
      $e= new ChainedException('Message', new IllegalArgumentException('Arg'));
      $this->assertEquals('Message', $e->getMessage()) &&
      $this->assertInstanceOf('lang.IllegalArgumentException', $e->getCause()) &&
      $this->assertEquals('Arg', $e->cause->getMessage()) &&
      $this->assertFalse(!strstr($e->toString(), 'Caused by Exception lang.IllegalArgumentException (Arg)'));
    }

    /**
     * Tests number of common elements is reported in  toString() output
     *
     */
    #[@test]
    public function commonElements() {
      $e= new ChainedException('Message', new IllegalArgumentException('Arg'));
      $this->assertEquals(1, preg_match_all('/  ... [0-9]+ more/', $e->toString(), $matches));
    }

    /**
     * Tests number of common elements is reported in  toString() output
     *
     */
    #[@test]
    public function chainedCommonElements() {
      $e= new ChainedException('Message', new ChainedException('Message2', new IllegalArgumentException('Arg')));
      $this->assertEquals(2, preg_match_all('/  ... [0-9]+ more/', $e->toString(), $matches));
    }
  }
?>
