<?php namespace net\xp_framework\unittest\peer\sockets;

use unittest\TestCase;
use peer\Socket;
use lang\Runtime;

/**
 * TestCase
 *
 * @see      xp://peer.Socket
 */
abstract class AbstractSocketTest extends TestCase {
  protected static $bindAddress= array(null, -1);
  protected $fixture= null;

  /**
   * Callback for when server is connected
   *
   * @param  string $bindAddress
   */
  public static function connected($bindAddress) {
    self::$bindAddress= explode(':', $bindAddress);
  }

  /**
   * Callback for when server should be shut down
   */
  public static function shutdown() {
    $c= new Socket(self::$bindAddress[0], self::$bindAddress[1]);
    $c->connect();
    $c->write("HALT\n");
    $c->close();
  }
  
  /**
   * Creates a new client socket
   *
   * @param   string addr
   * @param   int port
   * @return  peer.Socket
   */
  protected abstract function newSocket($addr, $port);

  /**
   * Setup this test case
   */
  public function setUp() {
    $this->fixture= $this->newSocket(self::$bindAddress[0], self::$bindAddress[1]);
  }

  /**
   * Tears down this test case
   */
  public function tearDown() {
    $this->fixture->isConnected() && $this->fixture->close();
  }
  
  /**
   * Test
   *
   */
  #[@test]
  public function initiallyNotConnected() {
    $this->assertFalse($this->fixture->isConnected());
  }

  /**
   * Test connecting
   *
   */
  #[@test]
  public function connect() {
    $this->assertTrue($this->fixture->connect());
    $this->assertTrue($this->fixture->isConnected());
  }

  /**
   * Test connecting
   *
   */
  #[@test, @expect('peer.ConnectException')]
  public function connectInvalidPort() {
    $this->newSocket(self::$bindAddress[0], -1)->connect(0.1);
  }

  /**
   * Test connecting
   *
   */
  #[@test, @expect('peer.ConnectException')]
  public function connectInvalidHost() {
    $this->newSocket('@invalid', self::$bindAddress[1])->connect(0.1);
  }

  /**
   * Test connecting to port 49151 (IANA Reserved)
   *
   * @see   http://www.iana.org/assignments/port-numbers
   */
  #[@test, @expect('peer.ConnectException')]
  public function connectUnConnected() {
    $this->newSocket(self::$bindAddress[0], 49151)->connect(0.1);
  }

  /**
   * Test closing
   *
   */
  #[@test]
  public function closing() {
    $this->assertTrue($this->fixture->connect());
    $this->assertTrue($this->fixture->close());
    $this->assertFalse($this->fixture->isConnected());
  }

  /**
   * Test closing
   *
   */
  #[@test]
  public function closingNotConnected() {
    $this->assertFalse($this->fixture->close());
  }
  
  /**
   * Test EOF after closing
   *
   */
  #[@test]
  public function eofAfterClosing() {
    $this->assertTrue($this->fixture->connect());
    
    $this->fixture->write("ECHO EOF\n");
    $this->assertEquals("+ECHO EOF\n", $this->fixture->readBinary());
    
    $this->fixture->write("CLOS\n");
    $this->assertEquals('', $this->fixture->readBinary());

    $this->assertTrue($this->fixture->eof());
    $this->fixture->close();
    $this->assertFalse($this->fixture->eof());
  }

  /**
   * Test writing data
   *
   */
  #[@test]
  public function write() {
    $this->fixture->connect();
    $this->assertEquals(10, $this->fixture->write("ECHO data\n"));
  }

  /**
   * Test writing data
   *
   */
  #[@test, @expect('peer.SocketException')]
  public function writeUnConnected() {
    $this->fixture->write('Anything');
  }

  /**
   * Test writing data after EOF
   *
   */
  #[@test, @ignore('Writes still succeed after close - no idea why...')]
  public function writeAfterEof() {
    $this->fixture->connect();
    $this->fixture->write("CLOS\n");
    try {
      $this->fixture->write('Anything');
      $this->fail('No exception raised', null, 'peer.SocketException');
    } catch (\peer\SocketException $expected) {
      // OK
    }
  }

  /**
   * Test reading data w/ readLine()
   *
   */
  #[@test]
  public function readLine() {
    $this->fixture->connect();
    $this->fixture->write("ECHO data\n");
    $this->assertEquals("+ECHO data", $this->fixture->readLine());
  }

  /**
   * Test readLine()
   *
   */
  #[@test, @expect('peer.SocketException')]
  public function readLineUnConnected() {
    $this->fixture->readLine();
  }

  /**
   * Test reading after eof w/ readLine()
   *
   */
  #[@test]
  public function readLineOnEof() {
    $this->fixture->connect();
    $this->fixture->write("CLOS\n");
    $this->assertNull($this->fixture->readLine());
    $this->assertTrue($this->fixture->eof(), '<EOF>');
  }

  /**
   * Test reading multiple lines separated by \n
   *
   */
  #[@test]
  public function readLinesWithLineFeed() {
    $this->fixture->connect();
    $this->fixture->write("LINE 5 %0A\n");
    for ($i= 0; $i < 5; $i++) {
      $this->assertEquals('+LINE '.$i, $this->fixture->readLine(), 'Line #'.$i);
    }
    $this->assertEquals('+LINE .', $this->fixture->readLine());
  }

  /**
   * Test reading multiple lines separated by \r
   *
   */
  #[@test, @ignore('readLine() only works for \n or \r\n at the moment')]
  public function readLinesWithCarriageReturn() {
    $this->fixture->connect();
    $this->fixture->write("LINE 5 %0D\n");
    for ($i= 0; $i < 5; $i++) {
      $this->assertEquals('+LINE '.$i, $this->fixture->readLine(), 'Line #'.$i);
    }
    $this->assertEquals('+LINE .', $this->fixture->readLine());
  }

  /**
   * Test reading multiple lines separated by \r\n
   *
   */
  #[@test]
  public function readLinesWithCarriageReturnLineFeed() {
    $this->fixture->connect();
    $this->fixture->write("LINE 5 %0D%0A\n");
    for ($i= 0; $i < 5; $i++) {
      $this->assertEquals('+LINE '.$i, $this->fixture->readLine(), 'Line #'.$i);
    }
    $this->assertEquals('+LINE .', $this->fixture->readLine());
  }
  
  /**
   * Read exactly the specific amount of bytes.
   *
   * @param   int num
   * @return  string
   */
  protected function readBytes($num) {
    $bytes= '';
    do {
      $bytes.= $this->fixture->readBinary($num- strlen($bytes));
    } while (strlen($bytes) < $num);
    return $bytes;
  }

  /**
   * Test readLine() and readBinary() in conjunction
   *
   */
  #[@test]
  public function readLineAndBinary() {
    $this->fixture->connect();
    $this->fixture->write("LINE 3 %0D%0A\n");
    $this->assertEquals('+LINE 0', $this->fixture->readLine());
    $this->assertEquals("+LINE 1\r\n+LINE 2\r\n+LINE .\n", $this->readBytes(26));
  }

  /**
   * Test readLine() and readBinary() in conjunction
   *
   */
  #[@test]
  public function readLineAndBinaryWithMaxLen() {
    $this->fixture->connect();
    $this->fixture->write("LINE 3 %0D%0A\n");
    $this->assertEquals('+LINE 0', $this->fixture->readLine());
    $this->assertEquals("+LINE 1\r\n", $this->readBytes(9));
    $this->assertEquals("+LINE 2\r\n", $this->readBytes(9));
    $this->assertEquals('+LINE .', $this->fixture->readLine());
  }

  /**
   * Test reading data w/ read()
   *
   */
  #[@test]
  public function read() {
    $this->fixture->connect();
    $this->fixture->write("ECHO data\n");
    $this->assertEquals("+ECHO data\n", $this->fixture->read());
  }

  /**
   * Test read()
   *
   */
  #[@test, @expect('peer.SocketException')]
  public function readUnConnected() {
    $this->fixture->read();
  }

  /**
   * Test reading after eof w/ read()
   *
   */
  #[@test]
  public function readOnEof() {
    $this->fixture->connect();
    $this->fixture->write("CLOS\n");
    $this->assertNull($this->fixture->read());
    $this->assertTrue($this->fixture->eof(), '<EOF>');
  }

  /**
   * Test reading data w/ readBinary()
   *
   */
  #[@test]
  public function readBinary() {
    $this->fixture->connect();
    $this->fixture->write("ECHO data\n");
    $this->assertEquals("+ECHO data\n", $this->fixture->read());
  }

  /**
   * Test readBinary()
   *
   */
  #[@test, @expect('peer.SocketException')]
  public function readBinaryUnConnected() {
    $this->fixture->readBinary();
  }

  /**
   * Test reading after eof w/ readBinary()
   *
   */
  #[@test]
  public function readBinaryOnEof() {
    $this->fixture->connect();
    $this->fixture->write("CLOS\n");
    $this->assertEquals('', $this->fixture->readBinary());
    $this->assertTrue($this->fixture->eof(), '<EOF>');
  }

  /**
   * Test canRead()
   *
   */
  #[@test]
  public function canRead() {
    $this->fixture->connect();
    $this->assertFalse($this->fixture->canRead(0.1));
  }

  /**
   * Test canRead()
   *
   */
  #[@test, @expect('peer.SocketException')]
  public function canReadUnConnected() {
    $this->fixture->canRead(0.1);
  }

  /**
   * Test canRead()
   *
   */
  #[@test]
  public function canReadWithData() {
    $this->fixture->connect();
    $this->fixture->write("ECHO data\n");
    $this->assertTrue($this->fixture->canRead(0.1));
  }

  /**
   * Test getHandle()
   *
   */
  #[@test]
  public function getHandle() {
    $this->fixture->connect();
    $this->assertTrue(is_resource($this->fixture->getHandle()));
  }

  /**
   * Test getHandle()
   *
   */
  #[@test]
  public function getHandleAfterClose() {
    $this->fixture->connect();
    $this->fixture->close();
    $this->assertNull($this->fixture->getHandle());
  }

  /**
   * Test getHandle()
   *
   */
  #[@test]
  public function getHandleUnConnected() {
    $this->assertNull($this->fixture->getHandle());
  }

  /**
   * Test setTimeout()
   *
   */
  #[@test, @expect('peer.SocketTimeoutException')]
  public function readTimeout() {
    $this->fixture->connect();
    $this->fixture->setTimeout(0.1);
    $this->fixture->read();
  }

  /**
   * Test setTimeout()
   *
   */
  #[@test, @expect('peer.SocketTimeoutException')]
  public function readBinaryTimeout() {
    $this->fixture->connect();
    $this->fixture->setTimeout(0.1);
    $this->fixture->readBinary();
  }

  /**
   * Test setTimeout()
   *
   */
  #[@test, @expect('peer.SocketTimeoutException')]
  public function readLineTimeout() {
    $this->fixture->connect();
    $this->fixture->setTimeout(0.1);
    $this->fixture->readLine();
  }

  /**
   * Test getInputStream() method
   *
   */
  #[@test]
  public function inputStream() {
    $expect= '<response><type>status</type><payload><bool>true</bool></payload></response>';
    $this->fixture->connect();
    $this->fixture->write('ECHO '.$expect."\n");
    
    $si= $this->fixture->getInputStream();
    $this->assertTrue($si->available() > 0, 'available() > 0');
    $this->assertEquals('+ECHO '.$expect, $si->read(strlen($expect)+ strlen('+ECHO ')));
  }

  /**
   * Test remoteEndpoint() method
   */
  #[@test]
  public function remoteEndpoint() {
    $this->assertEquals(
      new \peer\SocketEndpoint(self::$bindAddress[0], self::$bindAddress[1]),
      $this->fixture->remoteEndpoint()
    );
  }

  /**
   * Test localEndpoint() method
   */
  #[@test]
  public function localEndpointForUnconnectedSocket() {
    $this->assertNull($this->fixture->localEndpoint());
  }

  /**
   * Test localEndpoint() method
   */
  #[@test]
  public function localEndpointForConnectedSocket() {
    $this->fixture->connect();
    $this->assertInstanceOf('peer.SocketEndpoint', $this->fixture->localEndpoint());
  }
}
