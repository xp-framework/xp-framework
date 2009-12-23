<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'io.Folder',
    'io.File',
    'lang.Runtime'
  );

  /**
   * TestCase
   *
   * @see      xp://io.File
   */
  class FileTest extends TestCase {

    /**
     * Return a file that is known to exist
     *
     * @return  string
     */
    protected function fileKnownToExist() {
      return realpath(Runtime::getInstance()->getExecutable()->getFilename());
    }
  
    /**
     * Test NUL character is not allowed
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function nulCharacterNotAllowedInFilename() {
      new File("editor.txt\0.html");
    }

    /**
     * Test NUL character is not allowed
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function nulCharacterNotInTheBeginningOfFilename() {
      new File("\0editor.txt");
    }

    /**
     * Test creating a file with an empty filename
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function emptyFilenameNotAllowed() {
      new File('');
    }

    /**
     * Test creating a file with an empty filename
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function nullFilenameNotAllowed() {
      new File(NULL);
    }

    /**
     * Test composing filename by File("php://stdin")
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function filterScheme() {
      new File('php://filter/read=string.toupper|string.rot13/resource=http://www.example.comn');
    }

    /**
     * Test file object
     *
     */
    #[@test]
    public function newInstance() {
      $fn= $this->fileKnownToExist();
      $this->assertEquals($fn, create(new File($fn))->getURI());
    }

    /**
     * Test composing filename by File(Folder, string)
     *
     */
    #[@test]
    public function composingFromFolderAndString() {
      $fn= $this->fileKnownToExist();
      $this->assertEquals($fn, create(new File(new Folder(dirname($fn)), basename($fn)))->getURI());
    }

    /**
     * Test composing filename by File(Folder, string)
     *
     */
    #[@test]
    public function composingFromStringAndString() {
      $fn= $this->fileKnownToExist();
      $this->assertEquals($fn, create(new File(dirname($fn), basename($fn)))->getURI());
    }

    /**
     * Test composing filename by File(resource)
     *
     */
    #[@test]
    public function fromResource() {
      $this->assertNull(create(new File(fopen($this->fileKnownToExist(), 'r')))->getURI());
    }

    /**
     * Test composing filename by File("php://stderr")
     *
     */
    #[@test]
    public function stderr() {
      $this->assertEquals('php://stderr', create(new File('php://stderr'))->getURI());
    }

    /**
     * Test composing filename by File("php://stdout")
     *
     */
    #[@test]
    public function stdout() {
      $this->assertEquals('php://stdout', create(new File('php://stdout'))->getURI());
    }

    /**
     * Test composing filename by File("php://stdin")
     *
     */
    #[@test]
    public function stdin() {
      $this->assertEquals('php://stdin', create(new File('php://stdin'))->getURI());
    }
  }
?>
