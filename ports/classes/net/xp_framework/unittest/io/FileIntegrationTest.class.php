<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'io.File',
    'io.Folder',
    'lang.System'
  );

  /**
   * TestCase
   *
   * @see      xp://io.File
   */
  class FileIntegrationTest extends TestCase {
    protected static $temp= NULL;
    protected $file= NULL;
    protected $folder= NULL;

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
      if (($df= disk_free_space(self::$temp)) < 10240) {
        throw new PrerequisitesNotMetError('Not enough space available in $TEMP', NULL, array(sprintf(
          'df %s = %.0fk > 10k',
          self::$temp,
          $df / 1024
        )));
      }
    }

    /**
     * Creates file fixture, ensures it doesn't exist before tests start 
     * running, then creates folder fixture, ensuring it exists and is
     * empty.
     *
     */
    public function setUp() {
      $this->file= new File(self::$temp, '.xp-'.$this->getName().'file');
      if (file_exists($this->file->getURI())) {
        unlink($this->file->getURI());
      }

      $this->folder= new Folder($this->file->getPath(), '.xp-'.$this->getName().'folder');
      if (!file_exists($this->folder->getURI())) {
        mkdir($this->folder->getURI());
      } else {
        foreach (scandir($this->folder->getURI()) as $file) {
          if ('.' === $file || '..' === $file) continue;
          unlink($this->folder->getURI().$file);
        }
      }
    }
    
    /**
     * Deletes file and folder fixtures.
     *
     */
    public function tearDown() {
      $this->file->isOpen() && $this->file->close();

      if (file_exists($this->file->getURI())) {
        unlink($this->file->getURI());
      }

      if (file_exists($this->folder->getURI())) {
        foreach (scandir($this->folder->getURI()) as $file) {
          if ('.' === $file || '..' === $file) continue;
          unlink($this->folder->getURI().$file);
        }
        rmdir($this->folder->getURI());
      }
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
      $this->assertFalse($this->file->exists());
    }

    /**
     * Test exists() method
     *
     */
    #[@test]
    public function existsAfterCreating() {
      $this->writeData($this->file, NULL);
      $this->assertTrue($this->file->exists());
    }

    /**
     * Test exists() and unlink() methods
     *
     */
    #[@test]
    public function noLongerExistsAfterDeleting() {
      $this->writeData($this->file, NULL);
      $this->file->unlink();
      $this->assertFalse($this->file->exists());
    }
    
    /**
     * Test unlink() method
     *
     */
    #[@test, @expect('io.IOException')]
    public function cannotDeleteNonExistant() {
      $this->file->unlink();
    }

    /**
     * Test unlink() method
     *
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function cannotDeleteOpenFile() {
      $this->file->open(FILE_MODE_WRITE);
      $this->file->unlink();
    }

    /**
     * Test close() method
     *
     */
    #[@test, @expect('io.IOException')]
    public function cannotCloseUnopenedFile() {
      $this->file->close();
    }

    /**
     * Test writing to a file
     *
     */
    #[@test]
    public function write() {
      $this->assertEquals(5, $this->writeData($this->file, 'Hello'));
    }

    /**
     * Test writing to a file, then reading back the data
     *
     */
    #[@test]
    public function read() {
      with ($data= 'Hello'); {
        $this->writeData($this->file, $data);

        $this->file->open(FILE_MODE_READ);
        $this->assertEquals($data, $this->file->read(strlen($data)));
        $this->file->close();
      }
    }

    /**
     * Test writing to a file, then reading back the data
     *
     */
    #[@test]
    public function overwritingExistant() {
      with ($data= 'Hello World', $appear= 'This should not appear'); {
        $this->writeData($this->file, $appear);
        $this->writeData($this->file, $data);

        $this->file->open(FILE_MODE_READ);
        $this->assertEquals($data, $this->file->read(strlen($data)));
        $this->file->close();
      }
    }

    /**
     * Test writing to a file, then reading back the data
     *
     */
    #[@test]
    public function appendingToExistant() {
      with ($data= 'Hello World', $appear= 'This should appear'); {
        $this->writeData($this->file, $appear);
        $this->writeData($this->file, $data, TRUE);

        $this->assertEquals($appear.$data, $this->readData($this->file, strlen($appear) + strlen($data)));
      }
    }

    /**
     * Test a non-existant file cannot bee opened for reading
     *
     */
    #[@test, @expect('io.FileNotFoundException')]
    public function cannotOpenNonExistantForReading() {
      $this->file->open(FILE_MODE_READ);
    }

    /**
     * Test copy() method
     *
     */
    #[@test]
    public function copying() {
      with ($data= 'Hello World'); {
        $this->writeData($this->file, $data);

        $copy= new File($this->file->getURI().'.copy');
        $this->file->copy($copy->getURI());

        $read= $this->readData($copy);
        $copy->unlink();
        
        $this->assertEquals($data, $read);
      }
    }

    /**
     * Test copy() method
     *
     */
    #[@test]
    public function copyingOver() {
      with ($data= 'Hello World'); {
        $this->writeData($this->file, $data);

        $copy= new File($this->file->getURI().'.copy');
        $this->writeData($copy, 'Copy original content');
        $this->file->copy($copy->getURI());

        $read= $this->readData($copy);
        $copy->unlink();
        
        $this->assertEquals($data, $read);
      }
    }

    /**
     * Test copy() method
     *
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function cannotCopyOpenFile() {
      $this->file->open(FILE_MODE_WRITE);
      $this->file->copy('irrelevant');
    }

    /**
     * Test move() method
     *
     */
    #[@test]
    public function moving() {
      with ($data= 'Hello World'); {
        $this->writeData($this->file, $data);

        $target= new File($this->file->getURI().'.moved');
        $this->file->move($target->getURI());

        $read= $this->readData($target);
        $target->unlink();
        
        $this->assertEquals($data, $read);
        
        // FIXME I don't think io.File should be updating its URI when 
        // move() is called. Because it does, this assertion fails!
        // $this->assertFalse($this->file->exists()); 
      }
    }

    /**
     * Test move() method
     *
     */
    #[@test, @ignore('Breaks on Win2008 server, need special handling')]
    public function movingOver() {
      with ($data= 'Hello World'); {
        $this->writeData($this->file, $data);

        $target= new File($this->file->getURI().'.moved');
        $this->writeData($target, 'Target original content');
        $this->file->move($target->getURI());

        $read= $this->readData($target);
        $target->unlink();

        $this->assertEquals($data, $read);
        
        // FIXME I don't think io.File should be updating its URI when 
        // move() is called. Because it does, this assertion fails!
        // $this->assertFalse($this->file->exists()); 
      }
    }

    /**
     * Test move() method
     *
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function cannotMoveOpenFile() {
      $this->file->open(FILE_MODE_WRITE);
      $this->file->move('irrelevant');
    }

    /**
     * Test copy() method
     *
     */
    #[@test]
    public function copyingToAnotherFile() {
      $this->writeData($this->file, NULL);
      $target= new File($this->file->getURI().'.moved');
      $this->file->copy($target);
      $exists= $target->exists();
      $target->unlink();
      $this->assertTrue($exists);
    }

    /**
     * Test copy() method
     *
     */
    #[@test]
    public function copyingToAnotherFolder() {
      $this->writeData($this->file, NULL);
      $target= new File($this->folder, $this->file->getFilename());
      $this->file->copy($this->folder);
      $exists= $target->exists();
      $target->unlink();
      $this->assertTrue($exists);
    }

    /**
     * Test move() method
     *
     */
    #[@test]
    public function movingToAnotherFile() {
      $this->writeData($this->file, NULL);
      $target= new File($this->file->getURI().'.moved');
      $this->file->move($target);
      $exists= $target->exists();
      $target->unlink();
      $this->assertTrue($exists);
    }

    /**
     * Test move() method
     *
     */
    #[@test]
    public function movingToAnotherFolder() {
      $this->writeData($this->file, NULL);
      $target= new File($this->folder, $this->file->getFilename());
      $this->file->move($this->folder);
      $exists= $target->exists();
      $target->unlink();
      $this->assertTrue($exists);
    }
  }
?>
