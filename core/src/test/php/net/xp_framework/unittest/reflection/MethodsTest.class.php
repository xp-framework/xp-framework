<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'net.xp_framework.unittest.reflection.TestClass'
  );

  /**
   * TestCase
   *
   * @see      xp://lang.reflect.Method
   * @purpose  Unittest
   */
  class MethodsTest extends TestCase {
    protected
      $fixture  = NULL;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->fixture= XPClass::forName('net.xp_framework.unittest.reflection.TestClass');
    }
    
    /**
     * Assertion helper
     *
     * @param   lang.Generic var
     * @param   lang.Generic[] list
     * @throws  unittest.AssertionFailedError
     */
    protected function assertNotContained($var, $list) {
      foreach ($list as $i => $element) {
        if ($element->equals($var)) $this->fail('Element contained', 'Found at offset '.$i, NULL);
      }
    }

    /**
     * Assertion helper
     *
     * @param   lang.Generic var
     * @param   lang.Generic[] list
     * @throws  unittest.AssertionFailedError
     */
    protected function assertContained($var, $list) {
      foreach ($list as $i => $element) {
        if ($element->equals($var)) return;
      }
      $this->fail('Element not contained in list', NULL, $var);
    }

    /**
     * Tests the method reflection
     *
     * @see     xp://lang.XPClass#getMethods
     */
    #[@test]
    public function methods() {
      $methods= $this->fixture->getMethods();
      $this->assertInstanceOf('lang.reflect.Method[]', $methods);
      $this->assertContained($this->fixture->getMethod('equals'), $methods);
    }

    /**
     * Tests the method reflection
     *
     * @see     xp://lang.XPClass#getDeclaredMethods
     */
    #[@test]
    public function declaredMethods() {
      $methods= $this->fixture->getDeclaredMethods();
      $this->assertInstanceOf('lang.reflect.Method[]', $methods);
      $this->assertNotContained($this->fixture->getMethod('equals'), $methods);
    }
    
    /**
     * Helper method
     *
     * @param   int modifiers
     * @param   string method
     * @throws  unittest.AssertionFailedError
     */
    protected function assertModifiers($modifiers, $method) {
      $this->assertEquals($modifiers, $this->fixture->getMethod($method)->getModifiers());
    }

    /**
     * Tests method's declaring class
     *
     * @see     xp://lang.reflect.Method#getDeclaringClass
     */
    #[@test]
    public function declaredMethod() {
      $this->assertEquals(
        $this->fixture,
        $this->fixture->getMethod('setDate')->getDeclaringClass()
      );
    }

    /**
     * Tests method's declaring class
     *
     * @see     xp://lang.reflect.Method#getDeclaringClass
     */
    #[@test]
    public function inheritedMethod() {
      $this->assertEquals(
        $this->fixture->getParentClass(),
        $this->fixture->getMethod('clearDate')->getDeclaringClass()
      );
    }

    /**
     * Tests checking for a non-existant method
     *
     * @see     xp://lang.reflect.Method#hasMethod
     */
    #[@test]
    public function nonExistantMethod() {
      $this->assertFalse($this->fixture->hasMethod('@@nonexistant@@'));
    }

    /**
     * Tests retrieving a non-existant method
     *
     * @see     xp://lang.reflect.Method#getMethod
     */
    #[@test, @expect('lang.ElementNotFoundException')]
    public function getNonExistantMethod() {
      $this->fixture->getMethod('@@nonexistant@@');
    }

    /**
     * Tests constructor is not recognized as a method
     *
     * @see     xp://lang.reflect.Method#hasMethod
     */
    #[@test]
    public function checkConstructorIsNotAMethod() {
      $this->assertFalse($this->fixture->hasMethod('__construct'));
    }
    
    /**
     * Tests retrieving a non-existant method
     *
     * @see     xp://lang.reflect.Method#getMethod
     */
    #[@test, @expect('lang.ElementNotFoundException')]
    public function constructorIsNotAMethod() {
      $this->fixture->getMethod('__construct');
    }

    /**
     * Tests static initializer block is not recognized as a method
     *
     * @see     xp://lang.reflect.Method#hasMethod
     */
    #[@test]
    public function checkStaticInitializerIsNotAMethod() {
      $this->assertFalse($this->fixture->hasMethod('__static'));
    }
    
    /**
     * Tests static initializer block is not recognized as a method
     *
     * @see     xp://lang.reflect.Method#getMethod
     */
    #[@test, @expect('lang.ElementNotFoundException')]
    public function staticInitializerIsNotAMethod() {
      $this->fixture->getMethod('__static');
    }

    /**
     * Tests the method reflection
     *
     * @see     xp://lang.XPClass#getMethod
     */
    #[@test]
    public function publicMethod() {
      $this->assertModifiers(MODIFIER_PUBLIC, 'getMap');
    }

    /**
     * Tests the method reflection
     *
     * @see     xp://lang.XPClass#getMethod
     */
    #[@test]
    public function privateMethod() {
      $this->assertModifiers(MODIFIER_PRIVATE, 'defaultMap');
    }

    /**
     * Tests the method reflection
     *
     * @see     xp://lang.XPClass#getMethod
     */
    #[@test]
    public function protectedMethod() {
      $this->assertModifiers(MODIFIER_PROTECTED, 'clearMap');
    }

    /**
     * Tests the method reflection
     *
     * @see     xp://lang.XPClass#getMethod
     */
    #[@test]
    public function finalMethod() {
      $this->assertModifiers(MODIFIER_FINAL | MODIFIER_PUBLIC, 'setMap');
    }

    /**
     * Tests the method reflection
     *
     * @see     xp://lang.XPClass#getMethod
     */
    #[@test]
    public function staticMethod() {
      $this->assertModifiers(MODIFIER_STATIC | MODIFIER_PUBLIC, 'fromMap');
    }

    /**
     * Tests the method reflection
     *
     * @see     xp://lang.XPClass#getMethod
     */
    #[@test]
    public function abstractMethod() {
    
      // AbstractTestClass declares the method abstract (and therefore does not
      // implement it)
      $this->assertEquals(
        MODIFIER_PUBLIC | MODIFIER_ABSTRACT, 
        $this->fixture->getParentClass()->getMethod('getDate')->getModifiers()
      );

      // TestClass implements the method
      $this->assertModifiers(
        MODIFIER_PUBLIC, 
        'getDate'
      );
    }
    
    /**
     * Tests the method reflection for the getDate() method
     *
     * @see     xp://lang.XPClass#getMethod
     * @see     xp://lang.XPClass#hasMethod
     */
    #[@test]
    public function getDateMethod() {
      $this->assertTrue($this->fixture->hasMethod('getDate'));
      with ($method= $this->fixture->getMethod('getDate')); {
        $this->assertClass($method, 'lang.reflect.Method');
        $this->assertEquals('getDate', $method->getName(TRUE));
        $this->assertTrue($this->fixture->equals($method->getDeclaringClass()));
        $this->assertEquals('util.Date', $method->getReturnTypeName());
      }
    }

    /**
     * Tests the method Parameter reflection for the setDate() method
     *
     * @see     xp://lang.reflect.Routine#numParameters
     * @see     xp://lang.reflect.Routine#getParameter
     * @see     xp://lang.reflect.Parameter
     */
    #[@test]
    public function setDateMethodParameters() {
      with ($method= $this->fixture->getMethod('setDate')); {
        $this->assertEquals(1, $method->numParameters());
        if ($parameter= $method->getParameter(0)) {
          $this->assertClass($parameter, 'lang.reflect.Parameter');
          $this->assertEquals('date', $parameter->getName());
          $this->assertEquals('util.Date', $parameter->getTypeName());
          $this->assertEquals(XPClass::forName('util.Date'), $parameter->getType());
        }
        $this->assertNull($method->getParameter(1));
      }
    }

    /**
     * Tests invoking the setTrace() method which will always throw an 
     * IllegalStateException (which will be rewrapped as cause inside a
     * TargetInvocationException
     *
     * @see     xp://lang.reflect.TargetInvocationException
     * @see     xp://lang.reflect.Method#invoke
     */
    #[@test, @expect('lang.reflect.TargetInvocationException')]
    public function invokeSetTrace() {
      $this->fixture->getMethod('setTrace')->invoke($this->fixture->newInstance(), array(NULL));
    }

    /**
     * Tests invoking the setTrace() method on a wrong object
     *
     * @see     xp://lang.reflect.Method#invoke
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function invokeSetTraceOnWrongObject() {
      $this->fixture->getMethod('setTrace')->invoke(new Object(), array(NULL));
    }

    /**
     * Tests invoking static TestClass::initializerCalled
     *
     * @see     xp://lang.reflect.Method#invoke
     */
    #[@test]
    public function invokeStaticMethod() {
      $this->assertTrue($this->fixture->getMethod('initializerCalled')->invoke(NULL));
    }

    /**
     * Tests invoking private TestClass::defaultMap
     *
     * @see     xp://lang.reflect.Method#invoke
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function invokePrivateMethod() {
      $this->fixture->getMethod('defaultMap')->invoke($this->fixture->newInstance());
    }

    /**
     * Tests invoking protected TestClass::clearMap
     *
     * @see     xp://lang.reflect.Method#invoke
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function invokeProtectedMethod() {
      $this->fixture->getMethod('clearMap')->invoke($this->fixture->newInstance());
    }

    /**
     * Tests invoking abstract method
     *
     * @see     xp://lang.reflect.Method#invoke
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function invokeAbstractMethod() {
      XPClass::forName('net.xp_framework.unittest.reflection.AbstractTestClass')
        ->getMethod('getDate')
        ->invoke($this->fixture->newInstance())
      ;
    }

    /**
     * Tests invoking TestClass::setDate() - returns nothing
     *
     * @see     xp://lang.reflect.Method#invoke
     */
    #[@test]
    public function invokeMethodWithoutReturn() {
      $i= $this->fixture->newInstance();
      $d= new Date();
      $this->assertNull($this->fixture->getMethod('setDate')->invoke($i, array($d)));
      $this->assertEquals($d, $i->getDate());
    }

    /**
     * Tests void return value
     *
     * @see     xp://lang.reflect.Method#getReturnTypeName
     * @see     xp://lang.reflect.Method#getReturnType
     * @see     xp://net.xp_framework.unittest.reflection.TestClass#setDate
     */
    #[@test]
    public function voidReturnValue() {
      $this->assertEquals('void', $this->fixture->getMethod('setDate')->getReturnTypeName());
      $this->assertEquals(Type::$VOID, $this->fixture->getMethod('setDate')->getReturnType());
    }

    /**
     * Tests self return value
     *
     * @see     xp://lang.reflect.Method#getReturnTypeName
     * @see     xp://lang.reflect.Method#getReturnType
     * @see     xp://net.xp_framework.unittest.reflection.TestClass#withDate
     */
    #[@test]
    public function selfReturnValue() {
      $this->assertEquals('self', $this->fixture->getMethod('withDate')->getReturnTypeName());
      $this->assertEquals($this->fixture, $this->fixture->getMethod('withDate')->getReturnType());
    }

    /**
     * Tests bool return value
     *
     * @see     xp://lang.reflect.Method#getReturnTypeName
     * @see     xp://lang.reflect.Method#getReturnType
     * @see     xp://net.xp_framework.unittest.reflection.TestClass#initializerCalled
     */
    #[@test]
    public function boolReturnValue() {
      $this->assertEquals('bool', $this->fixture->getMethod('initializerCalled')->getReturnTypeName());
      $this->assertEquals(Primitive::$BOOLEAN, $this->fixture->getMethod('initializerCalled')->getReturnType());
    }
    
    /**
     * Tests generic return value
     *
     * @see     xp://lang.reflect.Method#getReturnTypeName
     * @see     xp://lang.reflect.Method#getReturnType
     * @see     xp://net.xp_framework.unittest.reflection.TestClass#getMap
     */
    #[@test]
    public function genericReturnValue() {
      $this->assertEquals('[:lang.Object]', $this->fixture->getMethod('getMap')->getReturnTypeName());
      $this->assertEquals(MapType::forName('[:lang.Object]'), $this->fixture->getMethod('getMap')->getReturnType());
    }

    /**
     * Tests string representation of a method with generic return value
     *
     * @see     xp://net.xp_framework.unittest.reflection.TestClass#getMap
     * @see     xp://lang.reflect.Method#toString
     */
    #[@test]
    public function getMapString() {
      $this->assertEquals(
        'public [:lang.Object] getMap()', 
        $this->fixture->getMethod('getMap')->toString()
      );
    }

    /**
     * Tests string representation of a method with generic return value
     *
     * @see     xp://net.xp_framework.unittest.reflection.TestClass#filterMap
     * @see     xp://lang.reflect.Method#toString
     */
    #[@test]
    public function filterMapString() {
      $this->assertEquals(
        'public util.collections.Vector<lang.Object> filterMap([string $pattern= null])',
        $this->fixture->getMethod('filterMap')->toString()
      );
    }

    /**
     * Tests string representation of a method with a class return value
     *
     * @see     xp://net.xp_framework.unittest.reflection.TestClass#getDate
     * @see     xp://lang.reflect.Method#toString
     */
    #[@test]
    public function getDateString() {
      $this->assertEquals(
        'public util.Date getDate()', 
        $this->fixture->getMethod('getDate')->toString()
      );
    }

    /**
     * Tests string representation of a protected method with void return value
     *
     * @see     xp://net.xp_framework.unittest.reflection.TestClass#clearMap
     * @see     xp://lang.reflect.Method#toString
     */
    #[@test]
    public function clearMapString() {
      $this->assertEquals(
        'protected void clearMap()', 
        $this->fixture->getMethod('clearMap')->toString()
      );
    }

    /**
     * Tests string representation of a public static method
     *
     * @see     xp://net.xp_framework.unittest.reflection.TestClass#fromMap
     * @see     xp://lang.reflect.Method#toString
     */
    #[@test]
    public function fromMapString() {
      $this->assertEquals(
        'public static net.xp_framework.unittest.reflection.TestClass fromMap([:lang.Object] $map)', 
        $this->fixture->getMethod('fromMap')->toString()
      );
    }

    /**
     * Tests string representation of method with throws documentation
     *
     * @see     xp://net.xp_framework.unittest.reflection.TestClass#setTrace
     * @see     xp://lang.reflect.Method#toString
     */
    #[@test]
    public function setTraceString() {
      $this->assertEquals(
        'public void setTrace(util.log.LogCategory $cat) throws lang.IllegalStateException', 
        $this->fixture->getMethod('setTrace')->toString()
      );
    }

    /**
     * Tests getExceptionNames method
     *
     * @see     xp://net.xp_framework.unittest.reflection.TestClass#setTrace
     * @see     xp://net.xp_framework.unittest.reflection.TestClass#currentTimestamp
     * @see     xp://lang.reflect.Method#getExceptionNames
     */
    #[@test]
    public function thrownExceptionNames() {
      $this->assertEquals(
        array('lang.IllegalArgumentException', 'lang.IllegalStateException'), 
        $this->fixture->getMethod('setDate')->getExceptionNames(),
        'with multiple throws'
      );
      $this->assertEquals(
        array('lang.IllegalStateException'), 
        $this->fixture->getMethod('setTrace')->getExceptionNames(),
        'with throws'
      );
      $this->assertEquals(
        array(), 
        $this->fixture->getMethod('currentTimestamp')->getExceptionNames(),
        'without throws'
      );
    }

    /**
     * Tests getExceptionTypes method
     *
     * @see     xp://lang.reflect.Method#getExceptionTypes
     */
    #[@test]
    public function thrownExceptionTypes() {
      $this->assertEquals(
        array(XPClass::forName('lang.IllegalArgumentException'), XPClass::forName('lang.IllegalStateException')), 
        $this->fixture->getMethod('setDate')->getExceptionTypes(),
        'with multiple throws'
      );
      $this->assertEquals(
        array(XPClass::forName('lang.IllegalStateException')), 
        $this->fixture->getMethod('setTrace')->getExceptionTypes(),
        'with throws'
      );
      $this->assertEquals(
        array(), 
        $this->fixture->getMethod('currentTimestamp')->getExceptionTypes(),
        'without throws'
      );
    }

    /**
     * Tests same methods are equal
     *
     * @see     xp://net.xp_framework.unittest.reflection.TestClass#setTrace
     * @see     xp://lang.reflect.Routine#equals
     */
    #[@test]
    public function equality() {
      $this->assertEquals(
        $this->fixture->getMethod('setTrace'),
        $this->fixture->getMethod('setTrace')
      );
    }

    /**
     * Tests equals() method does not choke on NULL
     *
     * @see     xp://lang.reflect.Routine#equals
     */
    #[@test]
    public function notEqualToNull() {
      $this->assertFalse($this->fixture->getMethod('setTrace')->equals(NULL));
    }

    /**
     * Tests inherited methods are not equal
     *
     * @see     xp://net.xp_framework.unittest.reflection.AbstractTestClass#getDate
     * @see     xp://net.xp_framework.unittest.reflection.TestClass#getDate
     * @see     xp://lang.reflect.Routine#equals
     */
    #[@test]
    public function inheritedMethodsAreNotEqual() {
      $this->assertNotEquals(
        $this->fixture->getMethod('getDate'),
        $this->fixture->getParentClass()->getMethod('getDate')
      );
    }

    /**
     * Tests method details for inherited interface methods
     *
     * @see     xp://io.collections.IOCollection
     * @see     xp://io.collections.IOElement#getOrigin
     */
    #[@test]
    public function methodDetailsForInheritedInterfaceMethod() {
      $this->assertEquals(
        'io.collections.IOCollection', 
        XPClass::forName('io.collections.IOCollection')->getMethod('getOrigin')->getReturnTypeName()
      );
    }

    /**
     * Tests util.collections.Map's method offsetGet() - which it
     * inherites from PHP's ArrayAccess interface - correctly
     * invokes its toString() method.
     *
     * @see     xp://util.collections.Map
     */
    #[@test]
    public function arrayAccessMethod() {
      $this->assertEquals(
        'public abstract var offsetGet(var $offset)', 
        XPClass::forName('util.collections.Map')->getMethod('offsetGet')->toString()
      );
    }

    /**
     * Test "self" in parameters
     *
     * @see     xp://lang.reflect.Parameter#getType
     * @see     xp://net.xp_framework.unittest.reflection.TestClass#isDateBefore
     */
    #[@test]
    public function selfParameterType() {
      $this->assertEquals(
        $this->fixture,
        $this->fixture->getMethod('isDateBefore')->getParameter(0)->getType()
      );
    }

    /**
     * Test "self" in parameters
     *
     * @see     xp://lang.reflect.Parameter#getTypeName
     * @see     xp://net.xp_framework.unittest.reflection.TestClass#isDateBefore
     */
    #[@test]
    public function selfParameterTypeName() {
      $this->assertEquals(
        'self',
        $this->fixture->getMethod('isDateBefore')->getParameter(0)->getTypeName()
      );
    }

    /**
     * Test "self" in parameters
     *
     * @see     xp://lang.reflect.Parameter#getTypeRestriction
     * @see     xp://net.xp_framework.unittest.reflection.TestClass#isDateBefore
     */
    #[@test]
    public function selfParameterTypeRestriction() {
      $this->assertEquals(
        $this->fixture,
        $this->fixture->getMethod('isDateBefore')->getParameter(0)->getTypeRestriction()
      );
    }
  }
?>
