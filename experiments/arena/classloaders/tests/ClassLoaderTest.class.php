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
     * Test
     *
     */
    #[@test]
    public function fileSystemClassLoader() {
      $cl= XPClass::forName('tests.classes.ClassOne')->getClassLoader();
      $this->assertClass($cl, 'lang.ClassLoader');
    }
  
    /**
     * Test
     *
     */
    #[@test]
    public function twoPublicClassesFromFileSystem() {
      $this->assertEquals(
        XPClass::forName('tests.classes.ClassOne')->getClassLoader(),
        XPClass::forName('tests.classes.ClassTwo')->getClassLoader()
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function archiveClassLoader() {
      $cl= XPClass::forName('tests.classes.ClassThree')->getClassLoader();
      $this->assertClass($cl, 'lang.archive.ArchiveClassLoader');
    }

    /**
     * Test
     *
     */
    #[@test]
    public function twoPublicClassesFromArchive() {
      $this->assertEquals(
        XPClass::forName('tests.classes.ClassThree')->getClassLoader(),
        XPClass::forName('tests.classes.ClassFour')->getClassLoader()
      );
    }
  }
?>
