<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'xp.compiler.types.ArrayTypeOf',
    'xp.compiler.types.TypeReflection'
  );

  /**
   * TestCase
   *
   * @see      xp://xp.compiler.types.ArrayTypeOf
   */
  class ArrayTypeOfTest extends TestCase {
    protected $fixture= NULL;
    
    /**
     * Set up test case - creates fixture
     *
     */
    public function setUp() {
      $this->fixture= new ArrayTypeOf(new TypeReflection(XPClass::forName('lang.XPClass')));
    }
  
    /**
     * Test name()
     *
     */
    #[@test]
    public function name() {
      $this->assertEquals('lang.XPClass[]', $this->fixture->name());
    }

    /**
     * Test literal()
     *
     */
    #[@test]
    public function literal() {
      $this->assertEquals('array', $this->fixture->literal());
    }

    /**
     * Test parent()
     *
     */
    #[@test]
    public function parent() {
      $this->assertEquals(new ArrayTypeOf(new TypeReflection(XPClass::forName('lang.Type'))), $this->fixture->parent());
    }

    /**
     * Test parent()
     *
     */
    #[@test]
    public function objectArrayHasNoParent() {
      $this->assertNull(create(new ArrayTypeOf(new TypeReflection(XPClass::forName('lang.Object'))))->parent());
    }

    /**
     * Test isSubclassOf()
     *
     */
    #[@test]
    public function isSubclassOfObjectArray() {
      $this->assertTrue($this->fixture->isSubclassOf(new ArrayTypeOf(new TypeReflection(XPClass::forName('lang.Object')))));
    }
 
    /**
     * Test isSubclassOf()
     *
     */
    #[@test]
    public function isNotSubclassOfObject() {
      $this->assertFalse($this->fixture->isSubclassOf(new TypeReflection(XPClass::forName('lang.Object'))));
    }
  }
?>
