<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses('util.profiling.unittest.TestCase', 'text.String');

  /**
   * Test assertion methods
   *
   * @purpose  Unit Test
   */
  class AssertionsTest extends TestCase {

    /**
     * Test assertTrue()
     *
     * @access  public
     */    
    #[@test]
    function trueIsTrue() {
      $this->assertTrue(TRUE);
    }

    /**
     * Test assertTrue()
     *
     * @access  public
     */    
    #[@test, @expect('util.profiling.unittest.AssertionFailedError')]
    function falseIsNotTrue() {
      $this->assertTrue(FALSE);
    }

    /**
     * Test assertFalse()
     *
     * @access  public
     */    
    #[@test]
    function falseIsFalse() {
      $this->assertFalse(FALSE);
    }

    /**
     * Test assertFalse()
     *
     * @access  public
     */    
    #[@test, @expect('util.profiling.unittest.AssertionFailedError')]
    function trueIsNotFalse() {
      $this->assertFalse(TRUE);
    }

    /**
     * Test assertNull()
     *
     * @access  public
     */    
    #[@test]
    function NullIsNull() {
      $this->assertNull(NULL);
    }

    /**
     * Test assertNull()
     *
     * @access  public
     */    
    #[@test, @expect('util.profiling.unittest.AssertionFailedError')]
    function falseIsNotNull() {
      $this->assertNull(FALSE);
    }

    /**
     * Test assertNull()
     *
     * @access  public
     */    
    #[@test, @expect('util.profiling.unittest.AssertionFailedError')]
    function zeroIsNotNull() {
      $this->assertNull(0);
    }

    /**
     * Test assertNull()
     *
     * @access  public
     */    
    #[@test, @expect('util.profiling.unittest.AssertionFailedError')]
    function emptyStringIsNotNull() {
      $this->assertNull('');
    }

    /**
     * Test assertNull()
     *
     * @access  public
     */    
    #[@test, @expect('util.profiling.unittest.AssertionFailedError')]
    function emptyArrayIsNotNull() {
      $this->assertNull(array());
    }

    /**
     * Test assertEquals() and assertNotEquals() invoke equals() methods 
     * on objects.
     *
     * @access  public
     */    
    #[@test]
    function equalsMethodIsInvoked() {
      $cl= &ClassLoader::getDefault();
      $class= &$cl->defineClass('AssertionsTest$.EqualsInvocationTest', 'class EqualsInvocationTest extends Object {
        var $equalsInvoked= 0;

        function equals(&$other) {
          $r= (
            is("EqualsInvocationTest", $other) && 
            $this->equalsInvoked == $other->equalsInvoked
          );
          $this->equalsInvoked++;
          return $r;
        }
      }');
      $instance= &$class->newInstance();
      
      // Create reference to equalsInvoked member. This is because assertEquals() 
      // will create a copy of the argument (because of its signature, where 
      // the expected and actual arguments are not pass-by-ref arguments; which
      // they can't be, because else assertEquals(TRUE, $other) wouldn't work...
      $invoked= &$instance->equalsInvoked;
      $this->assertEquals($instance, $instance);
      $this->assertNotEquals($instance, NULL);
      $this->assertEquals(2, $invoked);
    }

    /**
     * Test assertEquals() for integers
     *
     * @access  public
     */    
    #[@test]
    function integersAreEqual() {
      foreach (array(0, 1, -1) as $int) {
        $this->assertEquals($int, $int, $int);
      }
    }    

    /**
     * Test assertEquals() for strings
     *
     * @access  public
     */    
    #[@test]
    function stringsAreEqual() {
      foreach (array('', 'Hello', 'äöüß') as $str) {
        $this->assertEquals($str, $str, $str);
      }
    }    

    /**
     * Test assertEquals() for arrays
     *
     * @access  public
     */    
    #[@test]
    function arraysAreEqual() {
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
     * @access  public
     */    
    #[@test]
    function hashesAreEqual() {
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
     * @access  public
     */    
    #[@test]
    function hashesOrderNotRelevant() {
      $hash= array('&' => '&amp;', '"' => '&quot;');
      $reverse= array_reverse($hash, TRUE);
      $this->assertEquals($hash, $reverse, xp::stringOf($hash));
    }    

    /**
     * Test assertEquals() for text.String objects
     *
     * @access  public
     */    
    #[@test]
    function stringObjectsAreEqual() {
      foreach (array(new String(''), new String('Hello'), new String('äöüß')) as $str) {
        $this->assertEquals($str, $str, xp::stringOf($str));
      }
    }

    /**
     * Test assertNotEquals() for integers
     *
     * @access  public
     */    
    #[@test]
    function integersAreNotEqual() {
      foreach (array(-1, 1.0, NULL, FALSE, TRUE, '', array(), new String('1')) as $cmp) {
        $this->assertNotEquals(1, $cmp);
      }
    }    


    /**
     * Test assertNotEquals() for strings
     *
     * @access  public
     */    
    #[@test]
    function stringsAreNotEqual() {
      foreach (array(-1, 1.0, NULL, FALSE, TRUE, 1, array(), new String('1')) as $cmp) {
        $this->assertNotEquals('', $cmp);
      }
    }    

    /**
     * Test assertNotEquals() for arrays
     *
     * @access  public
     */    
    #[@test]
    function arraysAreNotEqual() {
      foreach (array(-1, 1.0, NULL, FALSE, TRUE, 1, array(1), new String('1')) as $cmp) {
        $this->assertNotEquals(array(), $cmp);
      }
    }    
  }
?>
