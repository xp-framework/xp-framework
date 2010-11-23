<?php
/* This class is part of the XP framework
 *
 * $Id: ChainingTest.class.php 14851 2010-09-23 17:41:52Z friebe $
 */

  $package= 'net.xp_lang.tests.execution.source';

  uses(
    'net.xp_lang.tests.execution.source.ExecutionTest',
    'util.collections.HashTable'
  );

  /**
   * Tests chaining
   *
   */
  class net·xp_lang·tests·execution·source·TypeRestrictionTest extends ExecutionTest {

    /**
     * Compile statements and return type
     *
     * @param   string type
     * @param   bool ok
     * @return  lang.Generic
     */
    protected function signature($signature) {
      $type= $this->define(
        'class', 
        ucfirst($this->name).'·'.($this->counter++), 
        NULL,
        '{ public bool accept('.$signature.') { return TRUE; }}',
        array('import util.collections.*;')
      );
      return $type->newInstance();
    }

    /**
     * Test passing a string to a string type hint
     *
     */
    #[@test]
    public function primitiveVsPrimitive() {
      $this->assertTrue($this->signature('string $arg', TRUE)->accept('string'));
    }

    /**
     * Test passing a string to an Object type hint
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function primitiveVsObject() {
      $this->signature('Object $arg')->accept('string');
    }

    /**
     * Test passing NULL to an Object type hint
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function nullVsObject() {
      $this->signature('Object $arg')->accept(NULL);
    }

    /**
     * Test passing NULL to an Object type hint with NULL default
     *
     */
    #[@test]
    public function nullVsObjectWithNullDefault() {
      $this->assertTrue($this->signature('Object $arg= null')->accept(NULL));
    }

    /**
     * Test passing an object to a primitive type hint
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function objectVsPrimitive() {
      $this->signature('string $arg')->accept($this);
    }

    /**
     * Test passing NULL to a primitive type hint
     *
     */
    #[@test]
    public function nulllVsPrimitive() {
      $this->assertTrue($this->signature('string $arg')->accept(NULL));
    }

    /**
     * Test passing a string to a string[] type hint
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function primitiveVsArray() {
      $this->signature('string[] $arg')->accept('string');
    }

    /**
     * Test passing an object to a string[] type hint
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function objectVsArray() {
      $this->signature('string[] $arg')->accept($this);
    }

    /**
     * Test generic version of util.collections.HashTable to a generic type hint
     *
     */
    #[@test]
    public function genericVsGenericHashTable() {
      $this->assertTrue($this->signature('HashTable<string, string> $arg')->accept(create('new HashTable<string, string>')));
    }

    /**
     * Test generic version of util.collections.HashTable to a generic type hint
     *
     */
    #[@test]
    public function genericHashTableVsGenericMap() {
      $this->assertTrue($this->signature('Map<string, string> $arg')->accept(create('new HashTable<string, string>')));
    }
  
    /**
     * Test non-generic vs. generic version of util.collections.HashTable
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function nonGenericVsGenericHashTable() {
      $this->signature('HashTable<string, string> $arg')->accept(new HashTable());
    }

    /**
     * Test generic version of util.collections.HashTable to a generic type hint
     *
     */
    #[@test]
    public function genericOfGenericsVsGenericOfGenericsHashTable() {
      $this->assertTrue($this->signature('HashTable<string, Vector<int> > $arg')->accept(create('new HashTable<string, Vector<int>>')));
    }

    /**
     * Test passing an object to a var type hint
     *
     */
    #[@test]
    public function objectVsVar() {
      $this->assertTrue($this->signature('var $arg')->accept($this));
    }

    /**
     * Test passing a string to a var type hint
     *
     */
    #[@test]
    public function primitiveVsVar() {
      $this->assertTrue($this->signature('var $arg')->accept('string'));
    }

    /**
     * Test passing NULL to a var type hint
     *
     */
    #[@test]
    public function nullVsVar() {
      $this->assertTrue($this->signature('var $arg')->accept(NULL));
    }

    /**
     * Test passing an array to a var type hint
     *
     */
    #[@test]
    public function arrayVsVar() {
      $this->assertTrue($this->signature('var $arg')->accept(array(1, 2, 3)));
    }
  }
?>
