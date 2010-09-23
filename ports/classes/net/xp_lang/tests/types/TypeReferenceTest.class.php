<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'xp.compiler.types.TypeReference'
  );

  /**
   * TestCase
   *
   * @see      xp://xp.compiler.types.TypeReference
   */
  class TypeReferenceTest extends TestCase {
  
    /**
     * Test name() method
     *
     */
    #[@test]
    public function nameWithoutPackage() {
      $decl= new TypeReference(new TypeName('TestCase'));
      $this->assertEquals('TestCase', $decl->name());
    }

    /**
     * Test name() method
     *
     */
    #[@test]
    public function literalWithoutPackage() {
      $decl= new TypeReference(new TypeName('TestCase'));
      $this->assertEquals('TestCase', $decl->literal());
    }

    /**
     * Test name() method
     *
     */
    #[@test]
    public function nameWithPackage() {
      $decl= new TypeReference(new TypeName('unittest.TestCase'));
      $this->assertEquals('unittest.TestCase', $decl->name());
    }

    /**
     * Test name() method
     *
     */
    #[@test]
    public function literalWithPackage() {
      $decl= new TypeReference(new TypeName('unittest.TestCase'));
      $this->assertEquals('TestCase', $decl->literal());
    }

    /**
     * Test isEnumerable() method
     *
     */
    #[@test]
    public function intIsNotEnumerable() {
      $decl= new TypeReference(new TypeName('int'));
      $this->assertFalse($decl->isEnumerable());
    }

    /**
     * Test isEnumerable() method
     *
     */
    #[@test]
    public function arrayIsEnumerable() {
      $decl= new TypeReference(new TypeName('int[]'));
      $this->assertTrue($decl->isEnumerable());
    }

    /**
     * Test isEnumerable() method
     *
     */
    #[@test]
    public function mapIsEnumerable() {
      $decl= new TypeReference(new TypeName('[:string]'));
      $this->assertTrue($decl->isEnumerable());
    }

    /**
     * Test getEnumerator() method
     *
     */
    #[@test]
    public function arrayEnumerator() {
      $enum= create(new TypeReference(new TypeName('int[]')))->getEnumerator();
      $this->assertEquals(new TypeName('int'), $enum->key);
      $this->assertEquals(new TypeName('int'), $enum->value);
    }

    /**
     * Test getEnumerator() method
     *
     */
    #[@test]
    public function mapEnumerator() {
      $enum= create(new TypeReference(new TypeName('[:string]')))->getEnumerator();
      $this->assertEquals(new TypeName('string'), $enum->key);
      $this->assertEquals(new TypeName('string'), $enum->value);
    }

    /**
     * Test hasIndexer() method
     *
     */
    #[@test]
    public function intDoesNotHaveAnIndexer() {
      $decl= new TypeReference(new TypeName('int'));
      $this->assertFalse($decl->hasIndexer());
    }

    /**
     * Test hasIndexer() method
     *
     */
    #[@test]
    public function arrayHasIndexer() {
      $decl= new TypeReference(new TypeName('int[]'));
      $this->assertTrue($decl->hasIndexer());
    }

    /**
     * Test hasIndexer() method
     *
     */
    #[@test]
    public function mapHasIndexer() {
      $decl= new TypeReference(new TypeName('[:string]'));
      $this->assertTrue($decl->hasIndexer());
    }

    /**
     * Test getIndexer() method
     *
     */
    #[@test]
    public function arrayIndexer() {
      $indexer= create(new TypeReference(new TypeName('int[]')))->getIndexer();
      $this->assertEquals(new TypeName('int'), $indexer->type);
      $this->assertEquals(new TypeName('int'), $indexer->parameter);
    }

    /**
     * Test getIndexer() method
     *
     */
    #[@test]
    public function mapIndexer() {
      $indexer= create(new TypeReference(new TypeName('[:string]')))->getIndexer();
      $this->assertEquals(new TypeName('string'), $indexer->type);
      $this->assertEquals(new TypeName('string'), $indexer->parameter);
    }
  }
?>
