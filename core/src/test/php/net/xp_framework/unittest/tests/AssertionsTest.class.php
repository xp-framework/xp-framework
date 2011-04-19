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
      foreach (array(new String(''), new String('Hello'), new String('äöüß', 'iso-8859-1')) as $str) {
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
     * Test assertInstanceOf()
     *
     */    
    #[@test]
    public function thisIsAnInstanceOfTestCase() {
      $this->assertInstanceOf('unittest.TestCase', $this);
    }

    /**
     * Test assertInstanceOf()
     *
     */    
    #[@test]
    public function thisIsAnInstanceOfTestCaseClass() {
      $this->assertInstanceOf(XPClass::forName('unittest.TestCase'), $this);
    }    

    /**
     * Test assertInstanceOf()
     *
     */    
    #[@test]
    public function thisIsAnInstanceOfObject() {
      $this->assertInstanceOf('lang.Object', $this);
    }    

    /**
     * Test assertInstanceOf()
     *
     */    
    #[@test]
    public function objectIsAnInstanceOfObject() {
      $this->assertInstanceOf('lang.Object', new Object());
    }    

    /**
     * Test assertInstanceOf()
     *
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function objectIsNotAnInstanceOfString() {
      $this->assertInstanceOf('lang.types.String', new Object());
    }    

    /**
     * Test assertInstanceOf()
     *
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function zeroIsNotAnInstanceOfGeneric() {
      $this->assertInstanceOf('lang.Generic', 0);
    }    

    /**
     * Test assertInstanceOf()
     *
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function nullIsNotAnInstanceOfGeneric() {
      $this->assertInstanceOf('lang.Generic', NULL);
    }    

    /**
     * Test assertInstanceOf()
     *
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function xpNullIsNotAnInstanceOfGeneric() {
      $this->assertInstanceOf('lang.Generic', xp::null());
    }    

    /**
     * Test assertInstanceOf()
     *
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function thisIsNotAnInstanceOfString() {
      $this->assertInstanceOf('lang.types.String', $this);
    }    

    /**
     * Test assertInstanceOf()
     *
     */    
    #[@test]
    public function thisIsAnInstanceOfGeneric() {
      $this->assertInstanceOf('lang.Generic', $this);
    }    

    /**
     * Test assertInstanceOf()
     *
     */    
    #[@test]
    public function zeroIsInstanceOfInt() {
      $this->assertInstanceOf('int', 0);
    }

    /**
     * Test assertInstanceOf()
     *
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function zeroPointZeroIsNotInstanceOfInt() {
      $this->assertInstanceOf('int', 0.0);
    }    

    /**
     * Test assertInstanceOf()
     *
     */    
    #[@test]
    public function nullIsInstanceOfVar() {
      $this->assertInstanceOf(Type::$VAR, NULL);
    }    

    /**
     * Test assertInstanceOf()
     *
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function nullIsNotInstanceOfVoidType() {
      $this->assertInstanceOf(Type::$VOID, NULL);
    }

    /**
     * Test assertInstanceOf()
     *
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function nullIsNotInstanceOfVoid() {
      $this->assertInstanceOf('void', NULL);
    }

    /**
     * Test assertInstanceOf()
     *
     */    
    #[@test]
    public function emptyArrayIsInstanceOfArray() {
      $this->assertInstanceOf('array', array());
    }

    /**
     * Test assertInstanceOf()
     *
     */    
    #[@test]
    public function intArrayIsInstanceOfArray() {
      $this->assertInstanceOf('array', array(1, 2, 3));
    }

    /**
     * Test assertInstanceOf()
     *
     */    
    #[@test]
    public function hashIsInstanceOfArray() {
      $this->assertInstanceOf('array', array('color' => 'green'));
    }

    /**
     * Test assertInstanceOf()
     *
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function nullIsNotInstanceOfArray() {
      $this->assertInstanceOf('array', NULL);
    }

    /**
     * Test assertInstanceOf()
     *
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function arrayListIsNotInstanceOfArray() {
      $this->assertInstanceOf('array', new ArrayList(1, 2, 3));
    }

    /**
     * Test assertInstanceOf() for strings
     *
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function primitiveIsNotAnInstanceOfStringClass() {
      $this->assertInstanceOf('string', new String());
    }    

    /**
     * Test assertEmpty() for an empty array
     *
     * @deprecated
     */    
    #[@test]
    public function emptyArrayEmpty() {
      $this->assertEmpty(array());
    }    

    /**
     * Test assertEmpty() for a non-empty array
     *
     * @deprecated
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function nonEmptyArrayEmpty() {
      $this->assertEmpty(array(1));
    }    

    /**
     * Test assertNotEmpty() for a non-empty array
     *
     * @deprecated
     */    
    #[@test]
    public function nonEmptyArrayNotEmpty() {
      $this->assertNotEmpty(array(0));
    }    

    /**
     * Test assertNotEmpty() for an empty array
     *
     * @deprecated
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function emptyArrayNotEmpty() {
      $this->assertNotEmpty(array());
    }    

    /**
     * Test assertClass() for NULLs
     *
     * @deprecated
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function nullIsNotAClass() {
      $this->assertClass(NULL, 'lang.Object');
    }    

    /**
     * Test assertClass() for strings
     *
     * @deprecated
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function primitiveIsNotOfStringClass() {
      $this->assertClass('string', 'lang.types.String');
    }    

    /**
     * Test assertClass() for lang.Object
     *
     * @deprecated
     */    
    #[@test]
    public function objectIsOfObjectClass() {
      $this->assertClass(new Object(), 'lang.Object');
    }    

    /**
     * Test assertClass() for this
     *
     * @deprecated
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function thisIsOfNotObjectClass() {
      $this->assertClass($this, 'lang.Object');
    }    

    /**
     * Test assertSubclass() for NULLs
     *
     * @deprecated
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function nullIsNotASubClass() {
      $this->assertClass(NULL, 'lang.Object');
    }    

    /**
     * Test assertSubclass() for lang.Object
     *
     * @deprecated
     */    
    #[@test]
    public function objectIsOfObjectSubclass() {
      $this->assertClass(new Object(), 'lang.Object');
    }    

    /**
     * Test assertSubclass() for this
     *
     * @deprecated
     */    
    #[@test]
    public function thisIsOfObjectSubclass() {
      $this->assertSubClass($this, 'lang.Object');
    }    

    /**
     * Test assertSubclass() for strings
     *
     * @deprecated
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function primitiveIsNotOfStringSubclass() {
      $this->assertClass('string', 'lang.types.String');
    }    

    /**
     * Test assertObject() for this
     *
     * @deprecated
     */    
    #[@test]
    public function thisIsAnObject() {
      $this->assertObject($this);
    }    

    /**
     * Test assertObject() for NULL
     *
     * @deprecated
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function nullIsNotAnObject() {
      $this->assertObject(NULL);
    }    

    /**
     * Test assertObject() for primitives
     *
     * @deprecated
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function primitiveIsNotAnObject() {
      $this->assertObject('string');
    }    

    /**
     * Test assertArray() for an empty array
     *
     * @deprecated
     */    
    #[@test]
    public function emptyArrayIsAnArray() {
      $this->assertArray(array());
    }    

    /**
     * Test assertArray() for a non-empty array
     *
     * @deprecated
     */    
    #[@test]
    public function arrayIsAnArray() {
      $this->assertArray(array(1, 2, 3));
    }    

    /**
     * Test assertArray() for an associative array
     *
     * @deprecated
     */    
    #[@test]
    public function hashIsAnArray() {
      $this->assertArray(array('key' => 'value'));
    }    

    /**
     * Test assertArray() for a lang.types.ArrayList
     *
     * @deprecated
     */    
    #[@test]
    public function arrayListIsAnArray() {
      $this->assertArray(new ArrayList());
    }    

    /**
     * Test assertArray() for a lang.Object
     *
     * @deprecated
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function objectIsNotAnArray() {
      $this->assertArray(new Object());
    }    

    /**
     * Test assertArray() for NULL
     *
     * @deprecated
     */    
    #[@test, @expect('unittest.AssertionFailedError')]
    public function nullIsNotAnArray() {
      $this->assertArray(NULL);
    }    
  }
?>
