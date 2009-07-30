<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'peer.Socket'
  );

  /**
   * TestCase
   *
   * @see      xp://peer.Socket
   */
  class SocketTest extends TestCase {
    const SERVER_ADDR = 'php3.de';

    /**
     * Test
     *
     */
    #[@test]
    public function initiallyNotConnected() {
      $s= new Socket(self::SERVER_ADDR, 80);
      $this->assertFalse($s->isConnected());
    }
  
    /**
     * Test connecting
     *
     */
    #[@test]
    public function connect() {
      $s= new Socket(self::SERVER_ADDR, 80);
      $this->assertTrue($s->connect());
      $this->assertTrue($s->isConnected());
    }

    /**
     * Test closing
     *
     */
    #[@test]
    public function closing() {
      $s= new Socket(self::SERVER_ADDR, 80);
      $this->assertTrue($s->connect());
      $this->assertTrue($s->close());
      $this->assertFalse($s->isConnected());
    }

  }
?>
