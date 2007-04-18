<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.Type',
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
  class BoxingTest extends TestCase {
  
    /**
     * Test hash table operations
     *
     * @param   mixed primitive
     * @throws  unittest.AssertionFailedError
     */
    protected function assertHashTableOperation($primitive) {
      with (
        $boxed= Type::boxed($primitive),
        $value= new String($this->name),
        $hash= new HashTable()
      ); {
      
        try {
          // Test keys
          $hash[$primitive]= $value;
          $this->assertEquals(array($boxed), $hash->keys());
          $this->assertTrue($hash->containsKey($primitive), 'Key '.$primitive.' not contained');
          $this->assertEquals($value, $hash[$primitive]);
          $this->assertEquals($value, $hash[$boxed]);

          // Test values
          $hash[$boxed]= $primitive;
          $this->assertEquals(array($boxed), $hash->keys());
          $this->assertTrue($hash->containsValue($primitive), 'Value '.$primitive.' not contained');
          $this->assertEquals($boxed, $hash[$primitive]);
          $this->assertEquals($boxed, $hash[$boxed]);
        } catch (AssertionFailedError $e) {
          $this->fail($e->getMessage().' in {'.xp::stringOf($hash).'}', $e->actual, $e->expect);
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

    /**
     * Test vector operations
     *
     * @param   mixed primitive
     * @throws  unittest.AssertionFailedError
     */
    protected function assertVectorOperation($primitive) {
      with (
        $boxed= Type::boxed($primitive),
        $vector= new Vector()
      ); {
      
        try {
          $vector[]= $primitive;
          $this->assertTrue($vector->contains($primitive));
          $this->assertTrue($vector->contains($boxed));
          $this->assertEquals($boxed, $vector[0]);
        } catch (AssertionFailedError $e) {
          $this->fail($e->getMessage().' in {'.xp::stringOf($vector).'}', $e->actual, $e->expect);
        }
      }
    }


    /**
     * Tests Vector
     *
     */
    #[@test]
    public function vectorString() {
      $this->assertVectorOperation('string');
    }
  
    /**
     * Tests Vector
     *
     */
    #[@test]
    public function vectorInteger() {
      $this->assertVectorOperation(1);
    }

    /**
     * Tests Vector
     *
     */
    #[@test]
    public function vectorDouble() {
      $this->assertVectorOperation(1.0);
    }

    /**
     * Tests Vector
     *
     */
    #[@test]
    public function vectorBoolean() {
      $this->assertVectorOperation(TRUE);
    }

    /**
     * Tests Vector
     *
     */
    #[@test]
    public function vectorArray() {
      $this->assertVectorOperation(array(1, 2, 3));
    }
  }
?>
