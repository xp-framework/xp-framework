<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses('unittest.TestCase', 'lang.types.String', 'lang.types.ArrayList');

  /**
   * Test assertion methods
   *
   * @purpose  Unit Test
   */
  class AssertionsTest extends TestCase {

    /**
     * Test assertTrue()
     *
     */    
    #[@test]
    public function trueIsTrue() {
      $this->assertTrue(TRUE);
    }

    /**
     * Test assertTrue()
     *
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function falseIsNotTrue() {
      $this->assertTrue(FALSE);
    }

    /**
     * Test assertFalse()
     *
     */    
    #[@test]
    public function falseIsFalse() {
      $this->assertFalse(FALSE);
    }

    /**
     * Test assertFalse()
     *
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function trueIsNotFalse() {
      $this->assertFalse(TRUE);
    }

    /**
     * Test assertNull()
     *
     */    
    #[@test]
    public function NullIsNull() {
      $this->assertNull(NULL);
    }

    /**
     * Test assertNull()
     *
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function falseIsNotNull() {
      $this->assertNull(FALSE);
    }

    /**
     * Test assertNull()
     *
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function zeroIsNotNull() {
      $this->assertNull(0);
    }

    /**
     * Test assertNull()
     *
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function emptyStringIsNotNull() {
      $this->assertNull('');
    }

    /**
     * Test assertNull()
     *
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function emptyArrayIsNotNull() {
      $this->assertNull(array());
    }

    /**
     * Test assertEquals() and assertNotEquals() invoke equals() methods 
     * on objects.
     *
     */    
    #[@test]
    public function equalsMethodIsInvoked() {
      $instance= newinstance('lang.Object', array(), '{
        public $equalsInvoked= 0;

        public function equals($other) {
          $this->equalsInvoked++;
          return $other instanceof self && $this->equalsInvoked == $other->equalsInvoked;
        }
      }');
     
      $this->assertEquals($instance, $instance);
      $this->assertNotEquals($instance, NULL);
      $this->assertEquals(2, $instance->equalsInvoked);
    }

    /**
     * Test assertEquals() for integers
     *
     */    
    #[@test]
    public function integersAreEqual() {
      foreach (array(0, 1, -1) as $int) {
        $this->assertEquals($int, $int, $int);
      }
    }    

    /**
     * Test assertEquals() for strings
     *
     */    
    #[@test]
    public function stringsAreEqual() {
      foreach (array('', 'Hello', 'äöüß') as $str) {
        $this->assertEquals($str, $str, $str);
      }
    }    

    /**
     * Test assertEquals() for arrays
     *
     */    
    #[@test]
    public function arraysAreEqual() {
      foreach (array(
        array(), 
        array(1, 2, 3),
        array(array(1), array(), array(-1, 4), array(new String('baz')))
      ) as $array) {
        $this->assertEquals($array, $array, xp::stringOf($array));
      }
    }    

    /**
     * Test assertEquals() for hashes
     *
     */    
    #[@test]
    public function hashesAreEqual() {
      foreach (array(
        array(), 
        array('foo' => 2), 
        array(array('bar' => 'baz'), array(), array('bool' => TRUE, 'bar' => new String('baz')))
      ) as $hash) {
        $this->assertEquals($hash, $hash, xp::stringOf($hash));
      }
    }    

    /**
     * Test hash order is not relevant
     *
     */    
    #[@test]
    public function hashesOrderNotRelevant() {
      $hash= array('&' => '&amp;', '"' => '&quot;');
      $this->assertEquals($hash, array_reverse($hash, TRUE), xp::stringOf($hash));
    }    

    /**
     * Test assertEquals() for lang.types.String objects
     *
     */    
    #[@test]
    public function stringObjectsAreEqual() {
      foreach (array(new String(''), new String('Hello'), new String('äöüß')) as $str) {
        $this->assertEquals($str, $str, xp::stringOf($str));
      }
    }

    /**
     * Test assertEquals() fails for FALSE and NULL
     *
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function differentNotTypesAreNotEqual() {
      $this->assertEquals(FALSE, NULL);
    }    

    /**
     * Test assertNotEquals() for integers
     *
     */    
    #[@test]
    public function integersAreNotEqual() {
      foreach (array(-1, 1.0, NULL, FALSE, TRUE, '', array(), new String('1')) as $cmp) {
        $this->assertNotEquals(1, $cmp);
      }
    }    

    /**
     * Test assertNotEquals() for strings
     *
     */    
    #[@test]
    public function stringsAreNotEqual() {
      foreach (array(-1, 1.0, NULL, FALSE, TRUE, 1, array(), new String('1')) as $cmp) {
        $this->assertNotEquals('', $cmp);
      }
    }    

    /**
     * Test assertNotEquals() for arrays
     *
     */    
    #[@test]
    public function arraysAreNotEqual() {
      foreach (array(-1, 1.0, NULL, FALSE, TRUE, 1, array(1), new String('1')) as $cmp) {
        $this->assertNotEquals(array(), $cmp);
      }
    }    

    /**
     * Test assertNotEquals() throws exceptions on equality
     *
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function sameIntegersAreEqual() {
      $this->assertNotEquals(1, 1);
    }    

    /**
     * Test assertEmpty() for an empty array
     *
     */    
    #[@test]
    public function emptyArrayEmpty() {
      $this->assertEmpty(array());
    }    

    /**
     * Test assertEmpty() for a non-empty array
     *
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function nonEmptyArrayEmpty() {
      $this->assertEmpty(array(1));
    }    

    /**
     * Test assertNotEmpty() for a non-empty array
     *
     */    
    #[@test]
    public function nonEmptyArrayNotEmpty() {
      $this->assertNotEmpty(array(0));
    }    

    /**
     * Test assertNotEmpty() for an empty array
     *
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function emptyArrayNotEmpty() {
      $this->assertNotEmpty(array());
    }    

    /**
     * Test assertClass() for NULLs
     *
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function nullIsNotAClass() {
      $this->assertClass(NULL, 'lang.Object');
    }    

    /**
     * Test assertClass() for strings
     *
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function primitiveIsNotOfStringClass() {
      $this->assertClass('string', 'lang.types.String');
    }    

    /**
     * Test assertClass() for lang.Object
     *
     */    
    #[@test]
    public function objectIsOfObjectClass() {
      $this->assertClass(new Object(), 'lang.Object');
    }    

    /**
     * Test assertClass() for this
     *
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function thisIsOfNotObjectClass() {
      $this->assertClass($this, 'lang.Object');
    }    

    /**
     * Test assertSubclass() for NULLs
     *
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function nullIsNotASubClass() {
      $this->assertClass(NULL, 'lang.Object');
    }    

    /**
     * Test assertSubclass() for lang.Object
     *
     */    
    #[@test]
    public function objectIsOfObjectSubclass() {
      $this->assertClass(new Object(), 'lang.Object');
    }    

    /**
     * Test assertSubclass() for this
     *
     */    
    #[@test]
    public function thisIsOfObjectSubclass() {
      $this->assertSubClass($this, 'lang.Object');
    }    

    /**
     * Test assertSubclass() for strings
     *
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function primitiveIsNotOfStringSubclass() {
      $this->assertClass('string', 'lang.types.String');
    }    

    /**
     * Test assertObject() for this
     *
     */    
    #[@test]
    public function thisIsAnObject() {
      $this->assertObject($this);
    }    

    /**
     * Test assertObject() for NULL
     *
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function nullIsNotAnObject() {
      $this->assertObject(NULL);
    }    

    /**
     * Test assertObject() for primitives
     *
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function primitiveIsNotAnObject() {
      $this->assertObject('string');
    }    

    /**
     * Test assertArray() for an empty array
     *
     */    
    #[@test]
    public function emptyArrayIsAnArray() {
      $this->assertArray(array());
    }    

    /**
     * Test assertArray() for a non-empty array
     *
     */    
    #[@test]
    public function arrayIsAnArray() {
      $this->assertArray(array(1, 2, 3));
    }    

    /**
     * Test assertArray() for an associative array
     *
     */    
    #[@test]
    public function hashIsAnArray() {
      $this->assertArray(array('key' => 'value'));
    }    

    /**
     * Test assertArray() for a lang.types.ArrayList
     *
     */    
    #[@test]
    public function arrayListIsAnArray() {
      $this->assertArray(new ArrayList());
    }    

    /**
     * Test assertArray() for a lang.Object
     *
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function objectIsNotAnArray() {
      $this->assertArray(new Object());
    }    

    /**
     * Test assertArray() for NULL
     *
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function nullIsNotAnArray() {
      $this->assertArray(NULL);
    }    
  }
?>
