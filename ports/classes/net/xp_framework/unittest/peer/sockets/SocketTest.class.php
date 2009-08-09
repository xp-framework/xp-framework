<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'peer.Socket',
    'lang.Runtime'
  );

  /**
   * TestCase
   *
   * @see      xp://peer.Socket
   */
  class SocketTest extends TestCase {
    const SERVER_ADDR= '127.0.0.1';
    const SERVER_PORT= 2122;
    
    protected static $serverProcess = NULL;
    protected $fixture= NULL;

    /**
     * Setup this test case
     *
     */
    public function setUp() {
      $this->fixture= new Socket(self::SERVER_ADDR, self::SERVER_PORT);
    }

    /**
     * Tears down this test case
     *
     */
    public function tearDown() {
      $this->fixture->isConnected() && $this->fixture->close();
    }

    /**
     * Starts server in background
     *
     */
    #[@beforeClass]
    public static function startServer() {

      // Start server process
      with ($rt= Runtime::getInstance()); {
        self::$serverProcess= $rt->getExecutable()->newInstance(array_merge(
          $rt->startupOptions()->asArguments(),
          array($rt->bootstrapScript()),
          array('net.xp_framework.unittest.peer.sockets.TestingServer', self::SERVER_ADDR, self::SERVER_PORT)
        ));
      }
      self::$serverProcess->in->close();

      // Check if startup succeeded
      $status= self::$serverProcess->out->readLine();
      if (!strlen($status) || '+' != $status{0}) {
        try {
          self::shutdownServer();
        } catch (IllegalStateException $e) {
          $status.= $e->getMessage();
        }
        throw new PrerequisitesNotMetError('Cannot start server: '.$status, NULL);
      }
    }

    /**
     * Shut down FTP server
     *
     */
    #[@afterClass]
    public static function shutdownServer() {

      // Tell the server to shut down
      try {
        $c= new Socket(self::SERVER_ADDR, self::SERVER_PORT);
        $c->connect();
        $c->write("HALT\n");
        $c->close();
      } catch (Throwable $ignored) {
        // Fall through, below should terminate the process anyway
      }

      $status= self::$serverProcess->out->readLine();
      if (!strlen($status) || '+' != $status{0}) {
        while ($l= self::$serverProcess->out->readLine()) {
          $status.= $l;
        }
        while ($l= self::$serverProcess->err->readLine()) {
          $status.= $l;
        }
        self::$serverProcess->close();
        throw new IllegalStateException($status);
      }
      self::$serverProcess->close();
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
     * Test writing data
     *
     */
    #[@test]
    public function write() {
      $this->fixture->connect();
      $this->assertEquals(10, $this->fixture->write("ECHO data\n"));
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
     * Test reading data w/ readBinary()
     *
     */
    #[@test]
    public function readBinary() {
      $this->fixture->connect();
      $this->fixture->write("ECHO data\n");
      $this->assertEquals("+ECHO data\n", $this->fixture->read());
    }
  }
?>
