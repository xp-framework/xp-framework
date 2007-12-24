<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses('unittest.TestCase', 'lang.types.String');

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

        function equals($other) {
          $r= (
            is(get_class($this), $other) && 
            $this->equalsInvoked == $other->equalsInvoked
          );
          $this->equalsInvoked++;
          return $r;
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
      $reverse= array_reverse($hash, TRUE);
      $this->assertEquals($hash, $reverse, xp::stringOf($hash));
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
  }
?>
