<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase'
  );

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class ClassLoaderTest extends TestCase {

    /**
     * Test "ClassOne" class is loaded from file system
     *
     */
    #[@test]
    public function fileSystemClassLoader() {
      $cl= XPClass::forName('tests.classes.ClassOne')->getClassLoader();
      $this->assertClass($cl, 'lang.ClassLoader');
    }
  
    /**
     * Test class loaders are equal for two classes loaded from the
     * file system.
     *
     */
    #[@test]
    public function twoClassesFromFileSystem() {
      $this->assertEquals(
        XPClass::forName('tests.classes.ClassOne')->getClassLoader(),
        XPClass::forName('tests.classes.ClassTwo')->getClassLoader()
      );
    }

    /**
     * Test "ClassThree" is loaded from the archive in "lib"
     *
     */
    #[@test]
    public function archiveClassLoader() {
      $cl= XPClass::forName('tests.classes.ClassThree')->getClassLoader();
      $this->assertClass($cl, 'lang.archive.ArchiveClassLoader');
    }

    /**
     * Test  class loaders are equal for two classes loaded from the
     * archive in "lib"
     *
     */
    #[@test]
    public function twoClassesFromArchive() {
      $this->assertEquals(
        XPClass::forName('tests.classes.ClassThree')->getClassLoader(),
        XPClass::forName('tests.classes.ClassFour')->getClassLoader()
      );
    }
  }
?>
