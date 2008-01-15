<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'io.streams.MemoryInputStream',
    'io.streams.MemoryOutputStream',
    'lang.Process',
    'lang.Runtime',
    'peer.ftp.FtpConnection'
  );

  /**
   * TestCase for FTP API.
   *
   * @see      xp://peer.ftp.FtpConnection
   * @purpose  Unittest
   */
  class IntegrationTest extends TestCase {
    protected static
      $serverProcess = NULL;

    /**
     * Sets up test case
     *
     */
    #[@beforeClass]
    public static function startFtpServer() {
      
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
            "peer.ftp.server.FtpConnectionListener",
            "peer.ftp.server.storage.FilesystemStorage"
          );

          $stor= new FilesystemStorage(\'%s\');
          $auth= newinstance("lang.Object", array(), \'{
            public function authenticate($user, $password) {
              return ("testtest" == $user.$password);
            }
          }\');

          $listener= newinstance("peer.ftp.server.FtpConnectionListener", array($stor, $auth), \'{
            public function onShutdown($event, $params) {
              $this->answer($event->stream, 200, "Shutting down");
              $this->server->terminate= TRUE;
            }
          }\');
          
          %d && $listener->setTrace(Logger::getInstance()
            ->getCategory()
            ->withAppender(new FileAppender(\'%s\'))
          );
          try {
            $s= new Server("127.0.0.1", 2121);
            $s->addListener($listener);
            $s->init();
          } catch (Throwable $e) {
            echo "- ", $e->getMessage(), "\n";
            exit;
          }
          echo "+ Service\n";
          $s->service();
          echo "+ Done\n";
        ?>', 
        addslashes(dirname(__FILE__).DIRECTORY_SEPARATOR.'ftproot'.DIRECTORY_SEPARATOR),
        isset($debug),
        addslashes($debug)
      ));

      // Start server process
      self::$serverProcess= new Process(
        Runtime::getInstance()->getExecutable()->getFileName(),
        array(' -dinclude_path="'.ini_get('include_path').'"')
      );
      self::$serverProcess->in->write($src);
      self::$serverProcess->in->close();

      // Check if startup succeeded
      $status= self::$serverProcess->out->readLine();
      if (!strlen($status) || '+' != $status{0}) {
        self::shutdownFtpServer();
        throw new PrerequisitesNotMetError($status, 'Cannot start FTP server');
      }
    }
    
    /**
     * Shut down FTP server
     *
     */
    #[@afterClass]
    public static function shutdownFtpServer() {
    
      // Tell the FTP server to shut down
      try {
        $c= new FtpConnection('ftp://test:test@127.0.0.1:2121');
        $c->connect();
        $c->sendCommand('SHUTDOWN');
        $c->close();
      } catch (Throwable $ignored) {
        // Fall through, below should terminate the process anyway
      }

      $status= self::$serverProcess->out->readLine();
      if (!strlen($status) || '+' != $status{0}) {
        while ($l= self::$serverProcess->out->readLine()) {
          $status.= $l;
        }
        self::$serverProcess->close();
        throw new IllegalStateException($status);
      }
      self::$serverProcess->close();
    }
    
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->conn= new FtpConnection('ftp://test:test@127.0.0.1:2121?passive=1&timeout=1');
    }

    /**
     * Sets up test case
     *
     */
    public function tearDown() {
      $this->conn->close();
    }
    
    /**
     * Test connecting and logging in
     *
     */
    #[@test]
    public function connect() {
      $this->conn->connect();
    }

    /**
     * Test connecting and logging in with incorrect credentials
     *
     */
    #[@test, @expect('peer.AuthenticationException')]
    public function incorrectCredentials() {
      create(new FtpConnection('ftp://test:INCORRECT@127.0.0.1:2121?timeout=1'))->connect();
    }

    /**
     * Test retrieving root directory
     *
     */
    #[@test]
    public function rootDir() {
      $this->conn->connect();
      with ($root= $this->conn->rootDir()); {
        $this->assertClass($root, 'peer.ftp.FtpDir');
        $this->assertEquals('/', $root->getName());
      }
    }

    /**
     * Test retrieving root directory's contents
     *
     */
    #[@test]
    public function entries() {
      $this->conn->connect();
      $entries= $this->conn->rootDir()->entries();
      $this->assertClass($entries, 'peer.ftp.FtpEntryList');
      foreach ($entries as $entry) {
        $this->assertSubClass($entry, 'peer.ftp.FtpEntry');
      }
    }

    /**
     * Test retrieving the ".trash" directory which is empty.(except for
     * the ".svn" directory).
     *
     */
    #[@test]
    public function dotTrashDir() {
      $this->conn->connect();
      with ($r= $this->conn->rootDir()); {
        $this->assertTrue($r->hasDir('.trash'));
        $dir= $r->getDir('.trash');
        $this->assertClass($dir, 'peer.ftp.FtpDir');
        $this->assertEquals('/.trash/', $dir->getName());
        $this->assertEquals(1, $dir->entries()->size());
      }
    }

    /**
     * Test retrieving the "htdocs" directory which is not empty.
     *
     */
    #[@test]
    public function htdocsDir() {
      $this->conn->connect();
      with ($r= $this->conn->rootDir()); {
        $this->assertTrue($r->hasDir('htdocs'));
        $dir= $r->getDir('htdocs');
        $this->assertClass($dir, 'peer.ftp.FtpDir');
        $this->assertEquals('/htdocs/', $dir->getName());
        $this->assertNotEquals(0, $dir->entries()->size());
      }
    }

    /**
     * Test checking for a non-existant directory
     *
     */
    #[@test]
    public function nonExistantDir() {
      $this->conn->connect();
      $this->assertFalse($this->conn->rootDir()->hasDir(':DOES_NOT_EXIST'));
    }

    /**
     * Test retrieving a non-existant directory raises an exception.
     *
     */
    #[@test, @expect('io.FileNotFoundException')]
    public function getNonExistantDir() {
      $this->conn->connect();
      $this->conn->rootDir()->getDir(':DOES_NOT_EXIST');
    }

    /**
     * Test retrieving the "htdocs/index.html" file
     *
     */
    #[@test]
    public function indexHtml() {
      $this->conn->connect();
      with ($htdocs= $this->conn->rootDir()->getDir('htdocs')); {
        $this->assertTrue($htdocs->hasFile('index.html'));
        $index= $htdocs->getFile('index.html');
        $this->assertClass($index, 'peer.ftp.FtpFile');
        $this->assertEquals('/htdocs/index.html', $index->getName());
      }
    }

    /**
     * Test checking for a non-existant file
     *
     */
    #[@test]
    public function nonExistantFile() {
      $this->conn->connect();
      $this->assertFalse($this->conn->rootDir()->getDir('htdocs')->hasFile(':DOES_NOT_EXIST'));
    }

    /**
     * Test retrieving a non-existant file raises an exception
     *
     */
    #[@test, @expect('io.FileNotFoundException')]
    public function getNonExistantFile() {
      $this->conn->connect();
      $this->conn->rootDir()->getDir('htdocs')->getFile(':DOES_NOT_EXIST');
    }

    /**
     * Test retrieving a directory with getFile() raises an exception
     *
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function directoryViaGetFile() {
      $this->conn->connect();
      $this->conn->rootDir()->getFile('htdocs');
    }

    /**
     * Test retrieving a file with getDir() raises an exception
     *
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function fileViaGetDir() {
      $this->conn->connect();
      $this->conn->rootDir()->getDir('htdocs')->getDir('index.html');
    }

    /**
     * Test uploading
     *
     */
    #[@test]
    public function uploadFile() {
      $this->conn->connect();
      
      try {
        $dir= $this->conn->rootDir()->getDir('htdocs');
        $file= $dir->file('name.txt')->uploadFrom(new MemoryInputStream($this->name));
        $this->assertTrue($file->exists());
        $this->assertEquals(strlen($this->name), $file->getSize());
        $file->delete();
      } catch (Throwable $e) {
        
        // Unfortunately, try { } finally does not exist...        
        $file->delete();
        throw $e;
      }
    }

    /**
     * Test downloading
     *
     */
    #[@test]
    public function downloadFile() {
      $this->conn->connect();
      
      $m= $this->conn
        ->rootDir()
        ->getDir('htdocs')
        ->getFile('index.html')
        ->downloadTo(new MemoryOutputStream())
      ;
      
      $this->assertEquals("<html/>\n", $m->getBytes());
    }
  }
?>
