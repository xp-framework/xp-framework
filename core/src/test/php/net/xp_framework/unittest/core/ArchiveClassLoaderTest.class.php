<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'lang.archive.ArchiveClassLoader',
    'lang.archive.Archive',
    'io.FileUtil'
  );

  /**
   * TestCase for archive class loading
   *
   * Relies on an archive.xar file existing in the resources directory
   * with the following contents:
   * <pre>
   *  $ xar tvf archive.xar
   *     92 test/ClassLoadedFromArchive.class.php
   *    104 test/package-info.xp
   * </pre>
   * 
   * @see   xp://lang.archive.ArchiveClassLoader
   */
  class ArchiveClassLoaderTest extends TestCase {
    protected $fixture= NULL;
    
    /**
     * Sets fixture to point to archive.xar from src/test/resources/
     */
    public function setUp() {
      $this->fixture= new ArchiveClassLoader(new Archive(
        $this->getClass()->getPackage()->getResourceAsStream('archive.xar')
      ));
    }

    /**
     * Test providesClass() method
     */
    #[@test]
    public function provides_class_in_archive() {
      $this->assertTrue($this->fixture->providesClass('test.ClassLoadedFromArchive'));
    }

    /**
     * Test providesClass() method
     */
    #[@test]
    public function does_not_provide_non_existant_class() {
      $this->assertFalse($this->fixture->providesClass('non.existant.Class'));
    }

    /**
     * Test providesPackage() method
     */
    #[@test]
    public function provides_package_in_archive() {
      $this->assertTrue($this->fixture->providesPackage('test'));
    }

    /**
     * Test providesPackage() method
     */
    #[@test]
    public function does_not_provide_non_existant_package() {
      $this->assertFalse($this->fixture->providesPackage('non.existant'));
    }

    /**
     * Test providesResource() method
     */
    #[@test]
    public function provides_resource_in_archive() {
      $this->assertTrue($this->fixture->providesResource('test/package-info.xp'));
    }

    /**
     * Test providesResource() method
     */
    #[@test]
    public function does_not_provide_non_existant_resource() {
      $this->assertFalse($this->fixture->providesResource('non/existant/resource.file'));
    }

    /**
     * Test loadClass() method
     */
    #[@test]
    public function load_existing_class_from_archive() {
      $this->assertInstanceOf('lang.XPClass', $this->fixture->loadClass('test.ClassLoadedFromArchive'));
    }

    /**
     * Test loadClass() method
     */
    #[@test, @expect('lang.ClassNotFoundException')]
    public function loading_non_existant_class_raises_exception() {
      $this->fixture->loadClass('non.existant.Class');
    }

    /**
     * Test getResource() method
     */
    #[@test]
    public function load_existing_resource_from_archive() {
      $contents= $this->fixture->getResource('test/package-info.xp');
      $this->assertEquals('<?php', substr($contents, 0, strpos($contents, "\n")));
    }

    /**
     * Test getResourceAsStream() method
     */
    #[@test]
    public function load_existing_resource_stream_from_archive() {
      $contents= FileUtil::getContents($this->fixture->getResourceAsStream('test/package-info.xp'));
      $this->assertEquals('<?php', substr($contents, 0, strpos($contents, "\n")));
    }

    /**
     * Test getResource() method
     */
    #[@test, @expect('lang.ElementNotFoundException')]
    public function load_non_existant_resource_from_archive() {
      $this->fixture->getResource('non/existant/resource.file');
    }

    /**
     * Test getResourceAsStream() method
     */
    #[@test, @expect('lang.ElementNotFoundException')]
    public function load_non_existant_resource_stream_from_archive() {
      $this->fixture->getResourceAsStream('non/existant/resource.file');
    }

    /**
     * Test packageContents() method
     */
    #[@test]
    public function test_package_contents() {
      $this->assertEquals(
        array('ClassLoadedFromArchive.class.php', 'package-info.xp'),
        $this->fixture->packageContents('test')
      );
    }

    /**
     * Test packageContents() method
     */
    #[@test]
    public function non_existant_package_contents() {
      $this->assertEquals(
        array(),
        $this->fixture->packageContents('non.existant')
      );
    }

    /**
     * Test packageContents() method
     */
    #[@test]
    public function root_package_contents() {
      $this->assertEquals(
        array('test/'),
        $this->fixture->packageContents(NULL)
      );
    }
  }
?>
