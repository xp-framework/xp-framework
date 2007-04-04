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
    public function twoPublicClassesFromArchive() {
      $this->assertEquals(
        XPClass::forName('tests.classes.ClassThree')->getClassLoader(),
        XPClass::forName('tests.classes.ClassFour')->getClassLoader()
      );
    }
  }
?>
