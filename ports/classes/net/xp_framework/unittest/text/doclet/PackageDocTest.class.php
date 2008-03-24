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
      $this->root= newinstance('text.doclet.RootDoc', array(), '{
        public function packageNamed($name) {
          $p= new PackageDoc($name);
          $p->setRoot($this);
          return $p;
        }
      }');
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
    public function ioIsContainingPackageForIoDotStreams() {
      $this->assertEquals(
        $this->root->packageNamed('io'),
        $this->root->packageNamed('io.streams')->containingPackage()
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
  }
?>
