<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'io.streams.FileInputStream',
    'io.FileUtil',
    'io.TempFile'
  );

  /**
   * TestCase
   *
   * @see      xp://io.streams.FileInputStream
   */
  class FileInputStreamTest extends TestCase {
    protected 
      $file = NULL;
  
    /**
     * Sets up test case - creates temporary file
     *
     */
    public function setUp() {
      try {
        $this->file= new TempFile();
        FileUtil::setContents($this->file, 'Created by FileInputStreamTest');
      } catch (IOException $e) {
        throw new PrerequisitesNotMetError('Cannot write temporary file', $e, array($this->file));
      }
    }
    
    /**
     * Tear down this test case - removes temporary file
     *
     */
    public function tearDown() {
      try {
        $this->file->isOpen() && $this->file->close();
        $this->file->unlink();
      } catch (IOException $ignored) {
        // Can't really do anything about it...
      }
    }
    
    /**
     * Test read() method
     *
     */
    #[@test]
    public function reading() {
      with ($stream= new FileInputStream($this->file)); {
        $this->assertEquals('Created by ', $stream->read(11));
        $this->assertEquals('FileInputStreamTest', $stream->read());
        $this->assertEquals('', $stream->read());
      }
    }

    /**
     * Test seek() and tell() methods
     *
     */
    #[@test]
    public function seeking() {
      with ($stream= new FileInputStream($this->file)); {
        $this->assertEquals(0, $stream->tell());
        $stream->seek(20);
        $this->assertEquals(20, $stream->tell());
        $this->assertEquals('StreamTest', $stream->read());
      }
    }

    /**
     * Test available() method
     *
     */
    #[@test]
    public function availability() {
      with ($stream= new FileInputStream($this->file)); {
        $this->assertNotEquals(0, $stream->available());
        $stream->read(30);
        $this->assertEquals(0, $stream->available());
      }
    }

    /**
     * Test file remains open when FileInputStream instance is deleted
     *
     */
    #[@test]
    public function delete() {
      with ($stream= new FileInputStream($this->file)); {
        $this->assertTrue($this->file->isOpen());
        delete($stream);
        $this->assertTrue($this->file->isOpen());
      }
    }

    /**
     * Test opening a file input stream with a non-existant file name
     *
     */
    #[@test, @expect('io.FileNotFoundException')]
    public function nonExistantFile() {
      new FileInputStream('::NON-EXISTANT::');
    }

    /**
     * Test reading after stream has been closed
     *
     */
    #[@test, @expect('io.IOException')]
    public function readingAfterClose() {
      with ($stream= new FileInputStream($this->file)); {
        $stream->close();
        $stream->read();
      }
    }

    /**
     * Test availability after stream has been closed
     *
     */
    #[@test, @expect('io.IOException')]
    public function availableAfterClose() {
      with ($stream= new FileInputStream($this->file)); {
        $stream->close();
        $stream->available();
      }
    }

    /**
     * Test closig an already closed stream
     *
     */
    #[@test, @expect('io.IOException')]
    public function doubleClose() {
      with ($stream= new FileInputStream($this->file)); {
        $stream->close();
        $stream->close();
      }
    }
  }
?>
