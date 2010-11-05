<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'io.File',
    'lang.System'
  );

  /**
   * TestCase
   *
   * @see      xp://io.File
   */
  class FileIntegrationTest extends TestCase {
    protected static $temp= NULL;
    protected $fixture= NULL;

    /**
     * Verifies TEMP directory is usable and there is enough space
     *
     */
    #[@beforeClass]
    public static function verifyTempDir() {
      self::$temp= System::tempDir();
      if (!is_writeable(self::$temp)) {
        throw new PrerequisitesNotMetError('$TEMP is not writeable', NULL, array(self::$temp.' +w'));
      }
      if (($df= disk_free_space(self::$temp)) < 1024) {
        throw new PrerequisitesNotMetError('Not enough space available in $TEMP', NULL, array(sprintf(
          'df %s = %.0fk > 1k',
          self::$temp,
          $df / 1024
        )));
      }
    }

    /**
     * Creates fixture
     *
     */
    public function setUp() {
      $this->fixture= new File(self::$temp, $this->getName());
      file_exists($this->fixture->getURI()) && unlink($this->fixture->getURI());
    }
    
    /**
     * Deletes fixture
     *
     */
    public function tearDown() {
      $this->fixture->isOpen() && $this->fixture->close();
    }
 
    /**
     * Fill a given file with data - that is, open it in write mode,
     * write the data if not NULL, then finally close it.
     *
     * @param   io.File file
     * @param   string data default NULL
     * @param   bool append default FALSE
     * @return  int number of written bytes or 0 if NULL data was given
     * @throws  io.IOException
     */
    protected function fillWith($file, $data= NULL, $append= FALSE) {
      $file->open($append ? FILE_MODE_APPEND : FILE_MODE_WRITE);
      if (NULL === $data) {
        $written= 0;
      } else {
        $written= $file->write($data);
      }
      $file->close();
      return $written;
    }

    /**
     * Test exists() method
     *
     */
    #[@test]
    public function doesNotExistYet() {
      $this->assertFalse($this->fixture->exists());
    }

    /**
     * Test exists() method
     *
     */
    #[@test]
    public function existsAfterCreating() {
      $this->fillWith($this->fixture, NULL);
      $this->assertTrue($this->fixture->exists());
    }

    /**
     * Test exists() and unlink() methods
     *
     */
    #[@test]
    public function noLongerExistsAfterDeleting() {
      $this->fillWith($this->fixture, NULL);
      $this->fixture->unlink();
      $this->assertFalse($this->fixture->exists());
    }
    
    /**
     * Test unlink() method
     *
     */
    #[@test, @expect('io.IOException')]
    public function cannotDeleteNonExistant() {
      $this->fixture->unlink();
    }

    /**
     * Test unlink() method
     *
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function cannotDeleteOpenFile() {
      $this->fixture->open(FILE_MODE_WRITE);
      $this->fixture->unlink();
    }

    /**
     * Test writing to a file
     *
     */
    #[@test]
    public function write() {
      $this->assertEquals(5, $this->fillWith($this->fixture, 'Hello'));
    }

    /**
     * Test writing to a file, then reading back the data
     *
     */
    #[@test]
    public function read() {
      with ($data= 'Hello'); {
        $this->fillWith($this->fixture, $data);

        $this->fixture->open(FILE_MODE_READ);
        $this->assertEquals($data, $this->fixture->read(strlen($data)));
        $this->fixture->close();
      }
    }

    /**
     * Test writing to a file, then reading back the data
     *
     */
    #[@test]
    public function overwritingExistant() {
      with ($data= 'Hello World', $appear= 'This should not appear'); {
        $this->fillWith($this->fixture, $appear);
        $this->fillWith($this->fixture, $data);

        $this->fixture->open(FILE_MODE_READ);
        $this->assertEquals($data, $this->fixture->read(strlen($data)));
        $this->fixture->close();
      }
    }

    /**
     * Test writing to a file, then reading back the data
     *
     */
    #[@test]
    public function appendingToExistant() {
      with ($data= 'Hello World', $appear= 'This should appear'); {
        $this->fillWith($this->fixture, $appear);
        $this->fillWith($this->fixture, $data, TRUE);

        $this->fixture->open(FILE_MODE_READ);
        $this->assertEquals($appear.$data, $this->fixture->read(strlen($appear) + strlen($data)));
        $this->fixture->close();
      }
    }

    /**
     * Test a non-existant file cannot bee opened for reading
     *
     */
    #[@test, @expect('io.FileNotFoundException')]
    public function cannotOpenNonExistantForReading() {
      $this->fixture->open(FILE_MODE_READ);
    }
  }
?>
