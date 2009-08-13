<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'peer.SocketTimeoutException'
  );

  /**
   * TestCase
   *
   * @see      xop://peer.SocketTimeoutException
   * @purpose  purpose
   */
  class SocketTimeoutExceptionTest extends TestCase {
  
    /**
     * Test getTimeout() method
     *
     */
    #[@test]
    public function getTimeout() {
      $this->assertEquals(
        1.0, 
        create(new SocketTimeoutException('', 1.0))->getTimeout()
      );
    }

    /**
     * Test compoundMessage() method
     *
     */
    #[@test]
    public function compoundMessage() {
      $this->assertEquals(
        'Exception peer.SocketTimeoutException (Read failed after 1.000 seconds)',
        create(new SocketTimeoutException('Read failed', 1.0))->compoundMessage()
      );
    }
  }
?>
