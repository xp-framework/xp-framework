<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'io.File',
    'io.FileUtil',
    'lang.System',
    'util.FilesystemPropertySource'
  );

  /**
   * Testcase for util.Properties class.
   *
   * @see   xp://util.FilesystemPropertySource
   */
  class FilesystemPropertySourceTest extends TestCase {
    protected $tempFile;
    protected $fixture;

    public function setUp() {
      $tempDir= realpath(System::tempDir());
      $this->fixture= new FilesystemPropertySource($tempDir);

      // Create a temporary ini file
      $this->tempFile= new File($tempDir, 'temp.ini');
      FileUtil::setContents($this->tempFile, "[section]\nkey=value\n");
    }
  
    public function tearDown() {
      $this->tempFile->unlink();
    }

    /**
     * Test provides()
     */
    #[@test]
    public function provides_existing_ini_file() {
      $this->assertTrue($this->fixture->provides('temp'));
    }

    /**
     * Test provides()
     */
    #[@test]
    public function does_not_provide_non_existant_ini_file() {
      $this->assertFalse($this->fixture->provides('@@non-existant@@'));
    }

    /**
     * Test fetch()
     */
    #[@test]
    public function fetch_existing_ini_file() {
      $this->assertEquals(
        new Properties($this->tempFile->getURI()),
        $this->fixture->fetch('temp')
      );
    }

    /**
     * Test fetch()
     */
    #[@test, @expect(class= 'lang.IllegalArgumentException', withMessage= '/No properties @@non-existant@@ found at .+/')]
    public function fetch_non_existant_ini_file() {
      $this->fixture->fetch('@@non-existant@@');
    }
  }
?>
