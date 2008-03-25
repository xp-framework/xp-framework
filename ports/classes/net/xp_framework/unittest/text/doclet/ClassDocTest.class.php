<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'text.doclet.ClassDoc',
    'text.doclet.RootDoc'
  );

  /**
   * TestCase
   *
   * @see      xp://text.doclet.ClassDoc
   * @purpose  Unittest
   */
  class ClassDocTest extends TestCase {
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
      $this->assertEquals('Object', $this->root->classNamed('lang.Object')->name());
    }

    /**
     * Test qualifiedName() method
     *
     */
    #[@test]
    public function qualifiedName() {
      $this->assertEquals('lang.Object', $this->root->classNamed('lang.Object')->qualifiedName());
    }

    /**
     * Test fully qualified classes' names
     *
     * @see   http://xp-framework.net/rfc/0037
     */
    #[@test]
    public function namesForFullyQualified() {
      with ($classdoc= $this->root->classNamed('lang.reflect.Parameter')); {
        $this->assertEquals('Parameter', $classdoc->name());
        $this->assertEquals('lang.reflect.Parameter', $classdoc->qualifiedName());
      }
    }

    /**
     * Test containingPackage() method
     *
     */
    #[@test]
    public function containingPackage() {
      $this->assertEquals(
        $this->root->packageNamed('lang'),
        $this->root->classNamed('lang.Object')->containingPackage()
      );
    }
  }
?>
