<?php
/* This class is part of the XP framework
 *
 * $Id: ChainedExceptionTest.class.php 10976 2007-08-27 17:13:54Z friebe $
 */

  namespace net::xp_framework::unittest::core;
 
  ::uses(
    'unittest.TestCase',
    'lang.ChainedException'
  );

  /**
   * Test ChainedException class
   *
   * @see      xp://util.ChainedException
   * @purpose  Unit Test
   */
  class ChainedExceptionTest extends unittest::TestCase {

    /**
     * Tests a ChainedException without a cause
     *
     */
    #[@test]
    public function withoutCause() {
      $e= new lang::ChainedException('Message');
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
      $e= new lang::ChainedException('Message', new lang::IllegalArgumentException('Arg'));
      $this->assertEquals('Message', $e->getMessage()) &&
      $this->assertClass($e->getCause(), 'lang.IllegalArgumentException') &&
      $this->assertEquals('Arg', $e->cause->getMessage()) &&
      $this->assertFalse(!strstr($e->toString(), 'Caused by Exception lang.IllegalArgumentException (Arg)'));
    }

    /**
     * Tests number of common elements is reported in  toString() output
     *
     */
    #[@test]
    public function commonElements() {
      $e= new lang::ChainedException('Message', new lang::IllegalArgumentException('Arg'));
      $this->assertEquals(1, preg_match_all('/  ... [0-9]+ more/', $e->toString(), $matches));
    }

    /**
     * Tests number of common elements is reported in  toString() output
     *
     */
    #[@test]
    public function chainedCommonElements() {
      $e= new lang::ChainedException('Message', new lang::ChainedException('Message2', new lang::IllegalArgumentException('Arg')));
      $this->assertEquals(2, preg_match_all('/  ... [0-9]+ more/', $e->toString(), $matches));
    }
  }
?>
