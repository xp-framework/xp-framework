<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'net.xp_framework.unittest.remote';

  uses(
    'unittest.TestCase',
    'lang.Process',
    'lang.Runtime',
    'peer.Socket',
    'lang.archive.Archive',
    'remote.Remote'
  );

  /**
   * TestCase for Remote API
   *
   * @see      xp://remote.Remote
   * @purpose  Unittest
   */
  class net·xp_framework·unittest·remote·IntegrationTest extends TestCase {
    protected static
      $serverProcess        = NULL,
      $clientClassesLoader  = NULL;

    protected
      $remote= NULL;

    /**
     * Sets up test case
     *
     */
    #[@beforeClass]
    public static function startApplicationServer() {
      $self= Runtime::getInstance()->getExecutable();
      $cmd= $self->getFilename();
      
      // Add include path (TODO: Runtime::startup options)
      $cmd.= ' -dinclude_path="'.ini_get('include_path').'"';
      
      // Log protocol messages (specify a filename instead of NULL in 
      // the next line to activate)
      $debug= NULL;
      
      // Create server implementation sourcecode
      $src= trim(sprintf('
        <?php
          require("lang.base.php");
          uses(
            "util.log.Logger",
            "util.log.FileAppender",
            "peer.server.Server", 
            "remote.server.EascProtocol",
            "remote.server.deploy.scan.FileSystemScanner"
          );

          // Add shutdown message handler
          EascMessageFactory::setHandler(61, newinstance("remote.server.message.EascMessage", array(), \'{
            public function getType() { 
              return 61; 
            }
            public function handle($protocol, $data) {
              Logger::getInstance()->getCategory()->debug("Shutting down");
              $protocol->server->terminate= TRUE; 
            }
          }\')->getClass());

          %2$d && Logger::getInstance()->getCategory()->withAppender(new FileAppender(\'%3$s\'));
          
          // Fire up server
          try {
            $proto= new EascProtocol(new FileSystemScanner(\'%1$s\'));
            $proto->initialize();

            $s= new Server("127.0.0.1", 2121);
            $s->setProtocol($proto);
            $s->setTcpNodelay(TRUE);
            $s->init();
          } catch (Throwable $e) {
            echo "- ", $e->getMessage(), "\n";
            exit;
          }
          echo "+ Service\n";
          $s->service();
          echo "+ Done\n";
        ?>', 
        addslashes(dirname(__FILE__).DIRECTORY_SEPARATOR.'deploy'.DIRECTORY_SEPARATOR),
        isset($debug),
        addslashes($debug)
      ));

      // Start server process
      self::$serverProcess= new Process($cmd);
      self::$serverProcess->in->write($src);
      self::$serverProcess->in->close();

      // Check if startup succeeded
      $status= self::$serverProcess->out->readLine();
      if (!strlen($status) || '+' != $status{0}) {
        self::shutdownApplicationServer();
        throw new PrerequisitesNotMetError($status, 'Cannot start application server');
      }

      // Add classloader with CalculatorBean client classes
      self::$clientClassesLoader= ClassLoader::registerLoader(new ArchiveClassLoader(new Archive(XPClass::forName(xp::nameOf(__CLASS__))
        ->getPackage()
        ->getPackage('deploy')
        ->getResourceAsStream('beans.test.CalculatorBean.xar')
      )));
    }
    
    /**
     * Shut down application server
     *
     */
    #[@afterClass]
    public static function shutdownApplicationServer() {
      ClassLoader::removeLoader(self::$clientClassesLoader);
    
      // Send shutdown message (this is not supported by live servers
      // but functionality added via EascMessageFactory::setHandler())
      try {
        $s= new Socket('127.0.0.1', 2121);
        $s->connect();
        $s->write(pack('Nc4Na*', DEFAULT_PROTOCOL_MAGIC_NUMBER, 1, 0, 61, FALSE, 0, NULL));
        $s->close();
      } catch (Throwable $ignored) {
        $e->printStackTrace();
        // Fall through, below should terminate the process anyway
      }

      $status= self::$serverProcess->out->readLine();
      if (!strlen($status) || '+' != $status{0}) {
        while ($l= self::$serverProcess->out->readLine()) {
          $status.= $l;
        }
        echo $status;
      }

      self::$serverProcess->close();
    }
    
    /**
     * Sets up this unittest
     *
     */
    public function setUp() {
      try {
        $this->remote= Remote::forName('xp://127.0.0.1:2121');
      } catch (RemoteException $e) {
        throw new PrerequisitesNotMetError('Cannot setup client/server communication', $e);
      }
    }
    
    /**
     * Test lookup
     *
     */
    #[@test]
    public function lookupCalculator() {
      $calc= $this->remote->lookup('xp/test/Calculator');
      $this->assertSubclass($calc, 'beans.test.Calculator');
    }

    /**
     * Test lookup
     *
     */
    #[@test, @expect('remote.RemoteException')]
    public function lookupNonExistant() {
      $this->remote->lookup(':DOES_NOT_EXIST');
    }

    /**
     * Test calling a method
     *
     */
    #[@test]
    public function callMethod() {
      $this->assertEquals(3, $this->remote->lookup('xp/test/Calculator')->add(1, 2));
    }

    /**
     * Test calling a method
     *
     */
    #[@test, @ignore('Fatals')]
    public function callNonExistantMethod() {
      $this->remote->lookup('xp/test/Calculator')->doesNotExist();
    }
  }
?>
