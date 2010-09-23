<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'xp.compiler.types.TypeName'
  );

  /**
   * TestCase
   *
   * @see      xp://xp.compiler.types.TypeName
   */
  class TypeNameTest extends TestCase {

    /**
     * Test isVariable()
     *
     */
    #[@test]
    public function varIsVariable() {
      $this->assertTrue(TypeName::$VAR->isVariable());
    }

    /**
     * Test isVariable()
     *
     */
    #[@test]
    public function voidIsNotVariable() {
      $this->assertFalse(TypeName::$VOID->isVariable());
    }

    /**
     * Test isVariable()
     *
     */
    #[@test]
    public function objectIsNotVariable() {
      $this->assertFalse(create(new TypeName('lang.Object'))->isVariable());
    }

    /**
     * Test isVoid()
     *
     */
    #[@test]
    public function varIsNotVoid() {
      $this->assertFalse(TypeName::$VAR->isVoid());
    }

    /**
     * Test isVoid()
     *
     */
    #[@test]
    public function voidIsVoid() {
      $this->assertTrue(TypeName::$VOID->isVoid());
    }

    /**
     * Test isVoid()
     *
     */
    #[@test]
    public function objectIsNotVoid() {
      $this->assertFalse(create(new TypeName('lang.Object'))->isVoid());
    }

    /**
     * Test isPrimitive()
     *
     */
    #[@test]
    public function intIsPrimitive() {
      $this->assertTrue(create(new TypeName('int'))->isPrimitive());
    }

    /**
     * Test isPrimitive()
     *
     */
    #[@test]
    public function objectIsNotPrimitive() {
      $this->assertFalse(create(new TypeName('lang.Object'))->isPrimitive());
    }

    /**
     * Test isArray()
     *
     */
    #[@test]
    public function intArrayIsArray() {
      $this->assertTrue(create(new TypeName('int[]'))->isArray());
    }

    /**
     * Test isArray()
     *
     */
    #[@test]
    public function intIsNotArray() {
      $this->assertFalse(create(new TypeName('int'))->isArray());
    }

    /**
     * Test isMap()
     *
     */
    #[@test]
    public function intMapIsMap() {
      $this->assertTrue(create(new TypeName('[:int]'))->isMap());
    }

    /**
     * Test isMap()
     *
     */
    #[@test]
    public function intIsNotMap() {
      $this->assertFalse(create(new TypeName('int'))->isMap());
    }

    /**
     * Test isMap()
     *
     */
    #[@test]
    public function intArrayIsNotMap() {
      $this->assertFalse(create(new TypeName('int[]'))->isMap());
    }

    /**
     * Test isGeneric()
     *
     */
    #[@test]
    public function genericListIsGeneric() {
      $this->assertTrue(create(new TypeName('List', array(new TypeName('T'))))->isGeneric());
    }

    /**
     * Test isGeneric()
     *
     */
    #[@test]
    public function arrayIsNotGeneric() {
      $this->assertFalse(create(new TypeName('T[]'))->isGeneric());
    }
  
    /**
     * Test compoundName()
     *
     */
    #[@test]
    public function intPrimitiveCompoundName() {
      $this->assertEquals('int', create(new TypeName('int'))->compoundName());
    }

    /**
     * Test compoundName()
     *
     */
    #[@test]
    public function stringArrayCompoundName() {
      $this->assertEquals('string[]', create(new TypeName('string[]'))->compoundName());
    }

    /**
     * Test compoundName()
     *
     */
    #[@test]
    public function objectClassCompoundName() {
      $this->assertEquals('lang.Object', create(new TypeName('lang.Object'))->compoundName());
    }

    /**
     * Test compoundName()
     *
     */
    #[@test]
    public function genericListCompoundName() {
      $this->assertEquals('List<T>', create(new TypeName('List', array(new TypeName('T'))))->compoundName());
    }

    /**
     * Test arrayComponentType()
     *
     */
    #[@test]
    public function arrayComponentType() {
      $this->assertEquals(new TypeName('string'), create(new TypeName('string[]'))->arrayComponentType());
    }

    /**
     * Test arrayComponentType()
     *
     */
    #[@test]
    public function arrayComponentTypeOfNonArray() {
      $this->assertEquals(NULL, create(new TypeName('string'))->arrayComponentType());
    }

    /**
     * Test mapComponentType()
     *
     */
    #[@test]
    public function mapComponentType() {
      $this->assertEquals(
        new TypeName('int'), 
        create(new TypeName('[:int]'))->mapComponentType()
      );
    }

    /**
     * Test mapComponentType()
     *
     */
    #[@test]
    public function mapComponentTypeOfNonMap() {
      $this->assertEquals(NULL, create(new TypeName('string'))->mapComponentType());
    }

    /**
     * Test isPlaceholder()
     *
     */
    #[@test]
    public function tIsPlaceHolderInListOfT() {
      $decl= new TypeName('List', array(new TypeName('T')));
      $this->assertTrue($decl->isPlaceholder(new TypeName('T')));
    }
 
    /**
     * Test isPlaceholder()
     *
     */
    #[@test]
    public function kIsNotPlaceHolderInListOfT() {
      $decl= new TypeName('List', array(new TypeName('T')));
      $this->assertFalse($decl->isPlaceholder(new TypeName('K')));
    }

    /**
     * Test isPlaceholder()
     *
     */
    #[@test]
    public function kAndVArePlaceHoldersInMapOfKV() {
      $decl= new TypeName('Map', array(new TypeName('K'), new TypeName('V')));
      $this->assertTrue($decl->isPlaceholder(new TypeName('K')), 'K');
      $this->assertTrue($decl->isPlaceholder(new TypeName('V')), 'V');
    }
  }
?>
