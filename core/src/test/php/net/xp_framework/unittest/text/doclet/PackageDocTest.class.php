<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'text.doclet.PackageDoc',
    'text.doclet.RootDoc'
  );

  /**
   * TestCase
   *
   * @see      xp://text.doclet.PackageDoc
   * @purpose  Unittest
   */
  class PackageDocTest extends TestCase {
    protected
      $root = NULL;

    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->root= new RootDoc();
    }

    /**
     * Test name() method
     *
     */
    #[@test]
    public function name() {
      $this->assertEquals('io', $this->root->packageNamed('io')->name());
    }

    /**
     * Test containingPackage() method
     *
     */
    #[@test]
    public function containingPackage() {
      $this->assertEquals(
        $this->root->packageNamed('io.collections'),
        $this->root->packageNamed('io.collections.iterate')->containingPackage()
      );
    }

    /**
     * Test containingPackage() method
     *
     */
    #[@test]
    public function ioIsATopLevelPackage() {
      $this->assertNull($this->root->packageNamed('io')->containingPackage());
    }

    /**
     * Test containingPackage() method
     *
     */
    #[@test]
    public function topLevelContainingPackage() {
      $this->assertEquals(
        $this->root->packageNamed('io'),
        $this->root->packageNamed('io.streams')->containingPackage()
      );
    }
    
    /**
     * Test contains() method
     *
     */
    #[@test]
    public function packageDoesNotContainItself() {
      $this->assertFalse($this->root->packageNamed('io')->contains(
        $this->root->packageNamed('io')
      ));
    }

    /**
     * Test contains() method
     *
     */
    #[@test]
    public function subPackageIsContained() {
      $this->assertTrue($this->root->packageNamed('io')->contains(
        $this->root->packageNamed('io.streams')
      ));
    }

    /**
     * Test contains() method
     *
     */
    #[@test]
    public function subPackageOfSubPackageIsNotContained() {
      $this->assertFalse($this->root->packageNamed('io')->contains(
        $this->root->packageNamed('io.collections.iterate')
      ));
    }

    /**
     * Test contains() method
     *
     */
    #[@test]
    public function parallelPackageIsNotContained() {
      $this->assertFalse($this->root->packageNamed('io.collections')->contains(
        $this->root->packageNamed('io.streams')
      ));
    }

    /**
     * Test sourceFile() method
     *
     */
    #[@test]
    public function sourceFile() {
      $file= $this->root->packageNamed('lang')->sourceFile();
      $this->assertEquals('package-info.xp', $file->getFilename());
    }
  }
?>
