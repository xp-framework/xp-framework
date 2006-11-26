<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase');

  /**
   * Tests the xp::stringOf() core utility
   *
   * @purpose  Testcase
   */
  class StringOfTest extends TestCase {

    /**
     * Static initializer
     *
     * @model   static
     * @access  public
     */
    public static function __static() {
      $cl= &ClassLoader::getDefault();
      $cl->defineClass('net.xp_framework.unittest.core.TestString', 'class TestString extends Object {
        function toString() {
          return "TestString(6) { String }";
        }
      }');
      $cl->defineClass('net.xp_framework.unittest.core.TestThis', 'class TestThis extends Object {
        function toString() {
          return xp::stringOf($this);
        }
      }');
    }
    
    /**
     * Returns the XPClass object for the testclass
     *
     * @access  protected
     * @return  &lang.XPClass<net.xp_framework.unittest.core.TestString>
     */
    public function &testClass() {
      return XPClass::forName('net.xp_framework.unittest.core.TestString');
    }

    /**
     * Tests string argument
     *
     * @access  public
     */
    #[@test]
    public function stringArgument() {
      $this->assertEquals('"Hello"', xp::stringOf($string= 'Hello'));
    }

    /**
     * Tests boolean argument
     *
     * @access  public
     */
    #[@test]
    public function booleanArgument() {
      $this->assertEquals('true', xp::stringOf($bool= TRUE));
      $this->assertEquals('false', xp::stringOf($bool= FALSE));
    }

    /**
     * Tests null argument
     *
     * @access  public
     */
    #[@test]
    public function nullArgument() {
      $this->assertEquals('null', xp::stringOf($null= NULL));
    }

    /**
     * Tests xp::null() argument
     *
     * @see     xp://net.xp_framework.unittest.core.NullTest
     * @access  public
     */
    #[@test]
    public function xpNullArgument() {
      $this->assertEquals('<null>', xp::stringOf(xp::null()));
    }

    /**
     * Tests numbers
     *
     * @see     xp://net.xp_framework.unittest.core.NullTest
     * @access  public
     */
    #[@test]
    public function numericArgument() {
      $this->assertEquals('1', xp::stringOf($int= 1));
      $this->assertEquals('-1', xp::stringOf($negativeInt= -1));
      $this->assertEquals('1.5', xp::stringOf($float= 1.5));
      $this->assertEquals('-1.5', xp::stringOf($negativeFloat= -1.5));
    }

    /**
     * Tests an object argument
     *
     * @access  public
     */
    #[@test]
    public function objectArgument() {
      $class= &$this->testClass();
      $this->assertEquals('TestString(6) { String }', xp::stringOf($class->newInstance()));
    }

    /**
     * Tests simple array
     *
     * @access  public
     */
    #[@test]
    public function simpleArrayArgument() {
      $this->assertEquals(
        "[\n  0 => 1\n  1 => 2\n  2 => 3\n]", 
        xp::stringOf($a= array(1, 2, 3))
      );
    }

    /**
     * Tests array of arrays
     *
     * @access  public
     */
    #[@test]
    public function arrayOfArraysArgument() {
      $this->assertEquals(
        "[\n  0 => [\n    0 => 1\n    1 => 2\n    2 => 3\n  ]\n]", 
        xp::stringOf($a= array(array(1, 2, 3)))
      );
    }

    /**
     * Tests simple array
     *
     * @access  public
     */
    #[@test]
    public function hashmapArgument() {
      $class= &$this->testClass();
      $this->assertEquals(
        "[\n  foo => \"bar\"\n  bar => 2\n  baz => TestString(6) { String }\n]", 
        xp::stringOf($a= array(
          'foo' => 'bar', 
          'bar' => 2, 
          'baz' => $class->newInstance()
        ))
      );
    }

    /**
     * Tests stdClass and Directory builtin classes in PHP
     *
     * @access  public
     */
    #[@test]
    public function builtinObjectsArgument() {
      $this->assertEquals("php.stdClass {\n}", xp::stringOf(new StdClass()));
      $this->assertEquals("php.Directory {\n}", xp::stringOf(new Directory('.')));
    }

    /**
     * Tests resource
     *
     * @access  public
     */
    #[@test]
    public function resourceArgument() {
      $fd= fopen('php://stdin', 'r');
      $this->assertMatches(xp::stringOf($fd), '/resource\(type= stream, id= [0-9]+\)/');
      fclose($fd);
    }

    /**
     * Tests recursion within an array
     *
     * @access  public
     */
    #[@test]
    public function arrayRecursion() {
      $a= array();
      $a[0]= 'Outer array';
      $a[1]= array();
      $a[1][0]= 'Inner array';
      $a[1][1]= &$a;
      $this->assertEquals(<<<__
[
  0 => "Outer array"
  1 => [
    0 => "Inner array"
    1 => [
      0 => "Outer array"
      1 => ->{:recursion:}
    ]
  ]
]
__
      , xp::stringOf($a));
    }

    /**
     * Tests recursion within an array
     *
     * @access  public
     */
    #[@test]
    public function objectRecursion() {
      $o= new StdClass();
      $o->child= new StdClass();
      $o->child->parent= &$o;
      $this->assertEquals(<<<__
php.stdClass {
  child => php.stdClass {
    parent => ->{:recursion:}
  }
}
__
      , xp::stringOf($o));
    }

    /**
     * Tests toString() isn't invoked recursively by sourcecode such as:
     * <code>
     *   class MaliciousRecursionGenerator extends Object {
     *     function toString() {
     *       return xp::stringOf($this);
     *     }
     *   }
     *
     *   echo xp::stringOf(new MaliciousRecursionGenerator());
     * </code>
     *
     * @access  public
     */
    #[@test]
    public function toStringRecursion() {
      $test= new TestThis();
      $this->assertEquals(
        $test->getClassName()." {\n  __id => \"".$test->__id."\"\n}",
        xp::stringOf($test)
      );
    }
    
    /**
     * Test repeated xp::stringOf invokations on the same object
     *
     * @access  public
     */
    #[@test]
    public function repeatedCalls() {
      $object= new Object();
      $stringRep= $object->toString();
      
      $this->assertEquals($stringRep, xp::stringOf($object), 'first');
      $this->assertEquals($stringRep, xp::stringOf($object), 'second');
    }
  }
?>
