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
   * @see   xp://net.xp_framework.unittest.peer.server.TestingServer
   * @see   xp://peer.server.Server
   */
  abstract class AbstractServerTest extends TestCase {
    protected static
      $serverProcess = NULL,
      $bindAddress   = array(NULL, -1);

    protected $conn= NULL;
    protected $client= NULL;

    /**
     * Setup this test case
     *
     */
    public function setUp() {
      $this->conn= new Socket(self::$bindAddress[0], self::$bindAddress[1]);
    }
    
    /**
     * Connect helper
     *
     */
    protected function connect() {
      $this->conn->connect();
      $this->conn->write("CLNT\n");
      $this->client= $this->conn->readLine();
    }
    
    /**
     * Tears down this test case
     *
     */
    public function tearDown() {
      if ($this->conn->isConnected()) {
        $this->conn->close();
        self::$serverProcess->err->readLine();
      }
    }

    /**
     * Starts server in background
     *
     * @param   string protocol
     */
    public static function startServerWith($protocol) {

      // Start server process
      with ($rt= Runtime::getInstance()); {
        self::$serverProcess= $rt->getExecutable()->newInstance(array_merge(
          $rt->startupOptions()->asArguments(),
          array($rt->bootstrapScript('class')),
          array('net.xp_framework.unittest.peer.server.TestingServer', $protocol)
        ));
      }
      self::$serverProcess->in->close();

      // Check if startup succeeded
      $status= self::$serverProcess->out->readLine();
      if (2 != sscanf($status, '+ Service %[0-9.]:%d', self::$bindAddress[0], self::$bindAddress[1])) {
        try {
          self::shutdownServer();
        } catch (IllegalStateException $e) {
          $status.= $e->getMessage();
        }
        throw new PrerequisitesNotMetError('Cannot start server: '.$status, NULL);
      }
    }

    /**
     * Shut down socket server
     *
     */
    #[@afterClass]
    public static function shutdownServer() {

      // Tell the server to shut down
      try {
        $c= new Socket(self::$bindAddress[0], self::$bindAddress[1]);
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
     * Assertion helper
     *
     * @param   string[] verbs
     * @throws  unittest.AssertionFailedError
     */
    protected function assertHandled($verbs) {
      $actual= $expected= array();
      foreach ($verbs as $verb) {
        $actual[]= self::$serverProcess->err->readLine();
        $expected[]= $verb.' '.$this->client;
      }
      $this->assertEquals($expected, $actual);
    }
  }
?>
