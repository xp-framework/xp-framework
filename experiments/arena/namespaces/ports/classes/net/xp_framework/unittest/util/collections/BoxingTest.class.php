<?php
/* This class is part of the XP framework
 *
 * $Id: BoxingTest.class.php 10173 2007-04-29 17:12:00Z friebe $ 
 */

  namespace net::xp_framework::unittest::util::collections;

  ::uses(
    'lang.Primitive',
    'lang.types.ArrayList',
    'util.collections.HashTable',
    'util.collections.Vector'
  );

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class BoxingTest extends unittest::TestCase {
  
    /**
     * Test hash table operations
     *
     * @param   mixed primitive
     * @throws  unittest.AssertionFailedError
     */
    protected function assertHashTableOperation($primitive) {
      ::with (
        $boxed= lang::Primitive::boxed($primitive),
        $value= new lang::types::String($this->name),
        $hash= new util::collections::HashTable()
      ); {
      
        try {
          $hash[$primitive]= $value;
          $this->assertEquals(array($boxed), $hash->keys());
          $this->assertTrue($hash->containsKey($primitive), 'Key '.$primitive.' not contained');
          $this->assertTrue($hash->containsKey($boxed), 'Key '.::xp::stringOf($boxed).' not contained');
          $this->assertEquals($value, $hash[$primitive]);
          $this->assertEquals($value, $hash[$boxed]);
        } catch (AssertionFailedError $e) {
          $this->fail($e->getMessage().' in {'.::xp::stringOf($hash).'}', $e->actual, $e->expect);
        }
      }
    }

    /**
     * Tests HashTable
     *
     */
    #[@test]
    public function hashTableString() {
      $this->assertHashTableOperation('string');
    }
  
    /**
     * Tests HashTable
     *
     */
    #[@test]
    public function hashTableInteger() {
      $this->assertHashTableOperation(1);
    }

    /**
     * Tests HashTable
     *
     */
    #[@test]
    public function hashTableDouble() {
      $this->assertHashTableOperation(1.0);
    }

    /**
     * Tests HashTable
     *
     */
    #[@test]
    public function hashTableBoolean() {
      $this->assertHashTableOperation(TRUE);
    }

    /**
     * Tests HashTable
     *
     */
    #[@test]
    public function hashTableArray() {
      $this->assertHashTableOperation(array(1, 2, 3));
    }
  }
?>
