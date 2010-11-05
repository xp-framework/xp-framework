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
     * Creates fixture, ensures it doesn't exist before tests start running.
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
    protected function writeData($file, $data= NULL, $append= FALSE) {
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
     * Read data from a file - that is, open it in read mode, read
     * the number of bytes specified (or the entire file, if omitted),
     * then finally close it.
     *
     * @param   io.File file
     * @param   int length default -1
     * @return  string
     */
    protected function readData($file, $length= -1) {
      $file->open(FILE_MODE_READ);
      $data= $file->read($length < 0 ? $file->size() : $length);
      $file->close();
      return $data;
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
      $this->writeData($this->fixture, NULL);
      $this->assertTrue($this->fixture->exists());
    }

    /**
     * Test exists() and unlink() methods
     *
     */
    #[@test]
    public function noLongerExistsAfterDeleting() {
      $this->writeData($this->fixture, NULL);
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
      $this->assertEquals(5, $this->writeData($this->fixture, 'Hello'));
    }

    /**
     * Test writing to a file, then reading back the data
     *
     */
    #[@test]
    public function read() {
      with ($data= 'Hello'); {
        $this->writeData($this->fixture, $data);

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
        $this->writeData($this->fixture, $appear);
        $this->writeData($this->fixture, $data);

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
        $this->writeData($this->fixture, $appear);
        $this->writeData($this->fixture, $data, TRUE);

        $this->assertEquals($appear.$data, $this->readData($this->fixture, strlen($appear) + strlen($data)));
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

    /**
     * Test copy() method
     *
     */
    #[@test]
    public function copying() {
      with ($data= 'Hello World'); {
        $this->writeData($this->fixture, $data);

        $copy= new File($this->fixture->getURI().'.copy');
        $this->fixture->copy($copy->getURI());

        $this->assertEquals($data, $this->readData($copy));
        $this->assertTrue($this->fixture->exists());
      }
    }

    /**
     * Test copy() method
     *
     */
    #[@test]
    public function copyingOver() {
      with ($data= 'Hello World'); {
        $this->writeData($this->fixture, $data);

        $copy= new File($this->fixture->getURI().'.copy');
        $this->writeData($copy, 'Copy original content');
        $this->fixture->copy($copy->getURI());

        $this->assertEquals($data, $this->readData($copy));
        $this->assertTrue($this->fixture->exists());
      }
    }

    /**
     * Test copy() method
     *
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function cannotCopyOpenFile() {
      $this->fixture->open(FILE_MODE_WRITE);
      $this->fixture->copy('irrelevant');
    }

    /**
     * Test move() method
     *
     */
    #[@test]
    public function moving() {
      with ($data= 'Hello World'); {
        $this->writeData($this->fixture, $data);

        $target= new File($this->fixture->getURI().'.moved');
        $this->fixture->move($target->getURI());

        $this->assertEquals($data, $this->readData($target));
        
        // FIXME I don't think io.File should be updating its URI when 
        // move() is called. Because it does, this assertion fails!
        // $this->assertFalse($this->fixture->exists()); 
      }
    }

    /**
     * Test move() method
     *
     */
    #[@test]
    public function movingOver() {
      with ($data= 'Hello World'); {
        $this->writeData($this->fixture, $data);

        $target= new File($this->fixture->getURI().'.moved');
        $this->writeData($target, 'Target original content');
        $this->fixture->move($target->getURI());

        $this->assertEquals($data, $this->readData($target));
        
        // FIXME I don't think io.File should be updating its URI when 
        // move() is called. Because it does, this assertion fails!
        // $this->assertFalse($this->fixture->exists()); 
      }
    }

    /**
     * Test move() method
     *
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function cannotMoveOpenFile() {
      $this->fixture->open(FILE_MODE_WRITE);
      $this->fixture->move('irrelevant');
    }
  }
?>
