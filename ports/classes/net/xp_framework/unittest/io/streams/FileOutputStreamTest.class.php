<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'io.streams.FileOutputStream',
    'io.FileUtil',
    'io.TempFile'
  );

  /**
   * TestCase
   *
   * @see      xp://io.streams.FileOutputStream
   */
  class FileOutputStreamTest extends TestCase {
    protected 
      $file = NULL;
  
    /**
     * Sets up test case - creates temporary file
     *
     */
    public function setUp() {
      try {
        $this->file= new TempFile();
        FileUtil::setContents($this->file, 'Created by FileOutputStreamTest');
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
     * Test write() method
     *
     */
    #[@test]
    public function writing() {
      with ($stream= new FileOutputStream($this->file), $buffer= 'Created by '.$this->name); {
        $stream->write($buffer);
        $this->assertEquals($buffer, FileUtil::getContents($this->file));
      }
    }

    /**
     * Test write() method
     *
     */
    #[@test]
    public function appending() {
      with ($stream= new FileOutputStream($this->file, TRUE)); {
        $stream->write('!');
        $this->assertEquals('Created by FileOutputStreamTest!', FileUtil::getContents($this->file));
      }
    }

    /**
     * Test file remains open when FileOutputStream instance is deleted
     *
     */
    #[@test]
    public function delete() {
      with ($stream= new FileOutputStream($this->file)); {
        $this->assertTrue($this->file->isOpen());
        delete($stream);
        $this->assertTrue($this->file->isOpen());
      }
    }

    /**
     * Test writing after stream has been closed
     *
     */
    #[@test, @expect('io.IOException')]
    public function writingAfterClose() {
      with ($stream= new FileOutputStream($this->file)); {
        $stream->close();
        $stream->write('');
      }
    }

    /**
     * Test closig an already closed stream
     *
     */
    #[@test, @expect('io.IOException')]
    public function doubleClose() {
      with ($stream= new FileOutputStream($this->file)); {
        $stream->close();
        $stream->close();
      }
    }
  }
?>
