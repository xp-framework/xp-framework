<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'io.Folder',
    'lang.System'
  );

  /**
   * TestCase
   *
   * @see      xp://io.Folder
   */
  class FolderTest extends TestCase {
    protected $temp= '';
    
    /**
     * Normalizes path by adding a trailing slash to the end if not already
     * existant.
     *
     * @param   string path
     * @return  string
     */
    protected function normalize($path) {
      return rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
    }
  
    /**
     * Sets up test case - initializes directory in %TEMP
     *
     */
    public function setUp() {
      $this->temp= $this->normalize(realpath(System::tempDir())).md5(uniqid()).'.xp'.DIRECTORY_SEPARATOR;
      if (is_dir($this->temp) && !rmdir($this->temp)) {
        throw new PrerequisitesNotMetError('Fixture directory exists, but cannot remove', NULL, $this->temp);
      }
    }
    
    /**
     * Deletes directory in %TEMP if existant
     *
     */
    public function tearDown() {
      is_dir($this->temp) && rmdir($this->temp);
    }

    /**
     * Test isOpen()
     *
     */
    #[@test]
    public function initiallyNotOpen() {
      $this->assertFalse(create(new Folder($this->temp))->isOpen());
    }

    /**
     * Test exists()
     *
     */
    #[@test]
    public function exists() {
      $this->assertFalse(create(new Folder($this->temp))->exists());
    }

    /**
     * Test create()
     *
     */
    #[@test]
    public function create() {
      $f= new Folder($this->temp);
      $f->create();
      $this->assertTrue($f->exists());
    }

    /**
     * Test unlink()
     *
     */
    #[@test]
    public function unlink() {
      $f= new Folder($this->temp);
      $f->create();
      $f->unlink();
      $this->assertFalse($f->exists());
    }

    /**
     * Test getURI()
     *
     */
    #[@test]
    public function uriOfNonExistantFolder() {
      $this->assertEquals($this->temp, create(new Folder($this->temp))->getURI());
    }
    
    /**
     * Test getURI()
     *
     */
    #[@test]
    public function uriOfExistantFolder() {
      $f= new Folder($this->temp);
      $f->create();
      $this->assertEquals($this->temp, $f->getURI());
    }

    /**
     * Test getURI()
     *
     */
    #[@test]
    public function uriOfDotFolder() {
      $f= new Folder($this->temp, '.');
      $this->assertEquals($this->temp, $f->getURI());
    }

    /**
     * Test getURI()
     *
     */
    #[@test]
    public function uriOfDotFolderTwoLevels() {
      $f= new Folder($this->temp, '.', '.');
      $this->assertEquals($this->temp, $f->getURI());
    }

    /**
     * Test getURI()
     *
     */
    #[@test]
    public function uriOfParentFolder() {
      $f= new Folder($this->temp, '..');
      $this->assertEquals($this->normalize(dirname($this->temp)), $f->getURI());
    }

    /**
     * Test getURI()
     *
     */
    #[@test]
    public function uriOfParentFolderOfSubFolder() {
      $f= new Folder($this->temp, 'sub', '..');
      $this->assertEquals($this->temp, $f->getURI());
    }

    /**
     * Test getURI()
     *
     */
    #[@test]
    public function uriOfParentFolderOfSubFolderTwoLevels() {
      $f= new Folder($this->temp, 'sub1', 'sub2', '..', '..');
      $this->assertEquals($this->temp, $f->getURI());
    }

    /**
     * Test getURI()
     *
     */
    #[@test]
    public function parentDirectoryOfRootIsRoot() {
      $f= new Folder(DIRECTORY_SEPARATOR, '..');
      $this->assertEquals($this->normalize(realpath(DIRECTORY_SEPARATOR)), $f->getURI());
    }

    /**
     * Test getURI()
     *
     */
    #[@test]
    public function parentDirectoryOfRootIsRootTwoLevels() {
      $f= new Folder(DIRECTORY_SEPARATOR, '..', '..');
      $this->assertEquals($this->normalize(realpath(DIRECTORY_SEPARATOR)), $f->getURI());
    }

    /**
     * Test getURI()
     *
     */
    #[@test]
    public function relativeDirectory() {
      $f= new Folder('tmp');
      $this->assertEquals($this->normalize($this->normalize(realpath('.')).'tmp'), $f->getURI());
    }

    /**
     * Test getURI()
     *
     */
    #[@test]
    public function relativeDotDirectory() {
      $f= new Folder('./tmp');
      $this->assertEquals($this->normalize($this->normalize(realpath('.')).'tmp'), $f->getURI());
    }

    /**
     * Test getURI()
     *
     */
    #[@test]
    public function relativeParentDirectory() {
      $f= new Folder('../tmp');
      $this->assertEquals($this->normalize($this->normalize(realpath('..')).'tmp'), $f->getURI());
    }

    /**
     * Test getURI()
     *
     */
    #[@test]
    public function dotDirectory() {
      $f= new Folder('.');
      $this->assertEquals($this->normalize(realpath('.')), $f->getURI());
    }

    /**
     * Test getURI()
     *
     */
    #[@test]
    public function parentDirectory() {
      $f= new Folder('..');
      $this->assertEquals($this->normalize(realpath('..')), $f->getURI());
    }
  }
?>
