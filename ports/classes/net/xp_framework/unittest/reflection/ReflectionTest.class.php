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
   * Test the XP reflection API
   *
   * @see      xp://lang.XPClass
   * @purpose  Testcase
   */
  class ReflectionTest extends TestCase {
    public
      $class  = NULL;
  
    /**
     * Setup method
     *
     */
    public function setUp() {
      $this->class= XPClass::forName('net.xp_framework.unittest.reflection.TestClass');
    }
 
    /**
     * Tests the getName() method
     *
     * @see     xp://lang.XPClass#getName
     */
    #[@test]
    public function name() {
      $this->assertEquals(
        'net.xp_framework.unittest.reflection.TestClass', 
        $this->class->getName()
      );
    }

    /**
     * Tests instanciation
     *
     * @see     xp://lang.XPClass#newInstance
     */
    #[@test]
    public function instanciation() {
      $instance= $this->class->newInstance(1);
      $this->assertObject($instance);
      $this->assertClass($instance, 'net.xp_framework.unittest.reflection.TestClass');
      $this->assertTrue($this->class->isInstance($instance));
    }
    
    /**
     * Tests subclass
     *
     * @see     xp://lang.XPClass#isSubclassOf
     */
    #[@test]
    public function subClass() {
      $this->assertTrue($this->class->isSubclassOf('lang.Object'));
      $this->assertFalse($this->class->isSubclassOf('util.Date'));
      $this->assertFalse($this->class->isSubclassOf('net.xp_framework.unittest.reflection.TestClass'));
    }
   
    /**
     * Tests the parent class
     *
     * @see     xp://lang.XPClass#getParentClass
     */
    #[@test]
    public function parentClass() {
      $parent= $this->class->getParentClass();
      $this->assertClass($parent, 'lang.XPClass');
      $this->assertEquals('net.xp_framework.unittest.reflection.AbstractTestClass', $parent->getName());
      $this->assertEquals('lang.Object', $parent->getParentClass()->getName());
      $this->assertNull($parent->getParentClass()->getParentClass());
    }

    /**
     * Tests interfaces
     *
     * @see     xp://lang.XPClass#isInterface
     * @see     xp://lang.XPClass#getInterfaces
     */
    #[@test]
    public function interfaces() {
      $this->assertFalse($this->class->isInterface());
      $interfaces= $this->class->getInterfaces();
      $this->assertArray($interfaces);
      foreach ($interfaces as $interface) {
        $this->assertClass($interface, 'lang.XPClass');
        $this->assertTrue($interface->isInterface());
      }
    }

    /**
     * Tests the constructor
     *
     * @see     xp://lang.XPClass#hasConstructor
     * @see     xp://lang.XPClass#getConstructor
     */
    #[@test]
    public function constructor() {
      $this->assertTrue($this->class->hasConstructor());
      $this->assertClass($this->class->getConstructor(), 'lang.reflect.Constructor');
    }

    /**
     * Tests constructor incovation
     *
     * @see     xp://lang.reflect.Constructor#newInstance
     */
    #[@test]
    public function constructorInvocation() {
      $instance= $this->class->getConstructor()->newInstance('1977-12-14');
      $this->assertEquals($this->class, $instance->getClass());
      $this->assertEquals(new Date('1977-12-14'), $instance->getDate());
    }

    /**
     * Tests abstract constructor incovation throws an exception
     *
     * @see     xp://lang.reflect.Constructor#newInstance
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function abstractConstructorInvocation() {
      XPClass::forName('net.xp_framework.unittest.reflection.AbstractTestClass')
        ->getConstructor()
        ->newInstance()
      ;
    }

    /**
     * Tests abstract constructor incovation throws an exception
     *
     * @see     xp://lang.reflect.Constructor#newInstance
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function newInstanceForAbstractClass() {
      XPClass::forName('net.xp_framework.unittest.reflection.AbstractTestClass')->newInstance();
    }

    /**
     * Tests the field reflection
     *
     * @see     xp://lang.XPClass#getFields
     */
    #[@test]
    public function fields() {
      $fields= $this->class->getFields();
      $this->assertArray($fields);
      foreach ($fields as $field) {
        $this->assertClass($field, 'lang.reflect.Field');
      }
    }

    /**
     * Tests field's declaring class
     *
     * @see     xp://lang.reflect.Field#getDeclaringClass
     */
    #[@test]
    public function declaredField() {
      $this->assertEquals(
        $this->class,
        $this->class->getField('map')->getDeclaringClass()
      );
    }

    /**
     * Tests field's declaring class
     *
     * @see     xp://lang.reflect.Field#getDeclaringClass
     */
    #[@test]
    public function inheritedField() {
      $this->assertEquals(
        $this->class->getParentClass(),
        $this->class->getField('inherited')->getDeclaringClass()
      );
    }

    /**
     * Helper method
     *
     * @param   int modifiers
     * @param   string field
     * @throws  unittest.AssertionFailedError
     */
    protected function assertFieldModifiers($modifiers, $fields) {
      $this->assertEquals($modifiers, $this->class->getField($fields)->getModifiers());
    }

    /**
     * Tests field modifiers
     *
     * @see     xp://lang.reflect.Field#getModifiers
     */
    #[@test]
    public function publicField() {
      $this->assertFieldModifiers(MODIFIER_PUBLIC, 'date');
    }

    /**
     * Tests field modifiers
     *
     * @see     xp://lang.reflect.Field#getModifiers
     */
    #[@test]
    public function protectedField() {
      $this->assertFieldModifiers(MODIFIER_PROTECTED, 'size');
    }

    /**
     * Tests field modifiers
     *
     * @see     xp://lang.reflect.Field#getModifiers
     */
    #[@test]
    public function privateField() {
      $this->assertFieldModifiers(MODIFIER_PRIVATE, 'factor');
    }

    /**
     * Tests field modifiers
     *
     * @see     xp://lang.reflect.Field#getModifiers
     */
    #[@test]
    public function staticField() {
      $this->assertFieldModifiers(MODIFIER_PUBLIC | MODIFIER_STATIC, 'initializerCalled');
    }

    /**
     * Tests the field reflection for the "date" field
     *
     * @see     xp://lang.XPClass#getField
     * @see     xp://lang.XPClass#hasField
     */
    #[@test]
    public function dateField() {
      $this->assertTrue($this->class->hasField('date'));
      with ($field= $this->class->getField('date')); {
        $this->assertClass($field, 'lang.reflect.Field');
        $this->assertEquals('date', $field->getName());
        $this->assertEquals('util.Date', $field->getType());
        $this->assertTrue($this->class->equals($field->getDeclaringClass()));
      }
    }

    /**
     * Tests retrieving the "date" field's value
     *
     * @see     xp://lang.reflect.Field#get
     */
    #[@test]
    public function dateFieldValue() {
      $this->assertClass($this->class->getField('date')->get($this->class->newInstance()), 'util.Date');
    }

    /**
     * Tests retrieving the "date" field's value on a wrong object
     *
     * @see     xp://lang.reflect.Field#get
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function dateFieldValueOnWrongObject() {
      $this->class->getField('date')->get(new Object());
    }
    
    /**
     * Tests the method reflection
     *
     * @see     xp://lang.XPClass#getMethods
     */
    #[@test]
    public function methods() {
      $methods= $this->class->getMethods();
      $this->assertArray($methods);
      foreach ($methods as $method) {
        $this->assertClass($method, 'lang.reflect.Method');
      }
    }
    
    /**
     * Helper method
     *
     * @param   int modifiers
     * @param   string method
     * @throws  unittest.AssertionFailedError
     */
    protected function assertMethodModifiers($modifiers, $method) {
      $this->assertEquals($modifiers, $this->class->getMethod($method)->getModifiers());
    }

    /**
     * Tests method's declaring class
     *
     * @see     xp://lang.reflect.Method#getDeclaringClass
     */
    #[@test]
    public function declaredMethod() {
      $this->assertEquals(
        $this->class,
        $this->class->getMethod('setDate')->getDeclaringClass()
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
        $this->class->getParentClass(),
        $this->class->getMethod('clearDate')->getDeclaringClass()
      );
    }

    /**
     * Tests the method reflection
     *
     * @see     xp://lang.XPClass#getMethod
     */
    #[@test]
    public function publicMethod() {
      $this->assertMethodModifiers(MODIFIER_PUBLIC, 'getMap');
    }

    /**
     * Tests the method reflection
     *
     * @see     xp://lang.XPClass#getMethod
     */
    #[@test]
    public function privateMethod() {
      $this->assertMethodModifiers(MODIFIER_PRIVATE, 'defaultMap');
    }

    /**
     * Tests the method reflection
     *
     * @see     xp://lang.XPClass#getMethod
     */
    #[@test]
    public function protectedMethod() {
      $this->assertMethodModifiers(MODIFIER_PROTECTED, 'clearMap');
    }

    /**
     * Tests the method reflection
     *
     * @see     xp://lang.XPClass#getMethod
     */
    #[@test]
    public function finalMethod() {
      $this->assertMethodModifiers(MODIFIER_FINAL | MODIFIER_PUBLIC, 'setMap');
    }

    /**
     * Tests the method reflection
     *
     * @see     xp://lang.XPClass#getMethod
     */
    #[@test]
    public function staticMethod() {
      $this->assertMethodModifiers(MODIFIER_STATIC | MODIFIER_PUBLIC, 'fromMap');
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
        $this->class->getParentClass()->getMethod('getDate')->getModifiers()
      );

      // TestClass implements the method
      $this->assertMethodModifiers(
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
      $this->assertTrue($this->class->hasMethod('getDate'));
      with ($method= $this->class->getMethod('getDate')); {
        $this->assertClass($method, 'lang.reflect.Method');
        $this->assertEquals('getDate', $method->getName(TRUE));
        $this->assertTrue($this->class->equals($method->getDeclaringClass()));
        $this->assertEquals('util.Date', $method->getReturnType());
      }
    }

    /**
     * Tests the method argument reflection for the setDate() method
     *
     * @see     xp://lang.reflect.Routine#numArguments
     * @see     xp://lang.reflect.Routine#getArgument
     * @see     xp://lang.reflect.Argument
     */
    #[@test]
    public function setDateMethodArguments() {
      with ($method= $this->class->getMethod('setDate')); {
        $this->assertEquals(1, $method->numArguments());
        if ($argument= $method->getArgument(0)) {
          $this->assertClass($argument, 'lang.reflect.Argument');
          $this->assertEquals('date', $argument->getName());
          $this->assertEquals('util.Date', $argument->getType());
        }
        $this->assertNull($method->getArgument(1));
      }
    }

    /**
     * Tests invoking the setTrace() method which will always throw an 
     * IllegalStateException.
     *
     * @see     xp://lang.reflect.Method#invoke
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function invokeSetTrace() {
      $this->class->getMethod('setTrace')->invoke($this->class->newInstance(), array(NULL));
    }

    /**
     * Tests invoking the setTrace() method on a wrong object
     *
     * @see     xp://lang.reflect.Method#invoke
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function invokeSetTraceOnWrongObject() {
      $this->class->getMethod('setTrace')->invoke(new Object(), array(NULL));
    }

    /**
     * Tests invoking static TestClass::initializerCalled
     *
     * @see     xp://lang.reflect.Method#invoke
     */
    #[@test]
    public function invokeStaticMethod() {
      $this->assertTrue($this->class->getMethod('initializerCalled')->invoke(NULL));
    }

    /**
     * Tests invoking private TestClass::defaultMap
     *
     * @see     xp://lang.reflect.Method#invoke
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function invokePrivateMethod() {
      $this->class->getMethod('defaultMap')->invoke($this->class->newInstance());
    }

    /**
     * Tests invoking protected TestClass::clearMap
     *
     * @see     xp://lang.reflect.Method#invoke
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function invokeProtectedMethod() {
      $this->class->getMethod('clearMap')->invoke($this->class->newInstance());
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
        ->invoke($this->class->newInstance())
      ;
    }

    /**
     * Tests invoking TestClass::setDate() - returns nothing
     *
     * @see     xp://lang.reflect.Method#invoke
     */
    #[@test]
    public function invokeMethodWithoutReturn() {
      $i= $this->class->newInstance();
      $d= new Date();
      $this->assertNull($this->class->getMethod('setDate')->invoke($i, array($d)));
      $this->assertEquals($d, $i->getDate());
    }

    /**
     * Tests annotations
     *
     * @see     xp://lang.XPClass#getAnnotations
     * @see     xp://lang.XPClass#hasAnnotations
     */
    #[@test]
    public function annotations() {
      $this->assertTrue($this->class->hasAnnotations());
      $annotations= $this->class->getAnnotations();
      $this->assertArray($annotations);
    }

    /**
     * Tests annotation "test"
     *
     * @see     xp://lang.XPClass#getAnnotation
     * @see     xp://lang.XPClass#hasAnnotation
     */
    #[@test]
    public function testAnnotation() {
      $this->assertTrue($this->class->hasAnnotation('test'));
      $this->assertEquals('Annotation', $this->class->getAnnotation('test'));
    }
    
    /**
     * Tests dynamic class loading via forName()
     *
     * @see     xp://lang.XPClass#forName
     */
    #[@test]
    public function forName() {
      $class= XPClass::forName('util.Date');
      $this->assertClass($class, 'lang.XPClass');
      $this->assertEquals('util.Date', $class->getName());
    }
    
    /**
     * Tests that forName() throws a lang.ClassNotFoundException when 
     * passed with the name of a nonexistant class
     *
     * @see     xp://lang.ClassNotFoundException
     * @see     xp://lang.XPClass#forName
     */
    #[@test, @expect('lang.ClassNotFoundException')]
    public function nonExistantforName() {
      $class= XPClass::forName('class.does.not.Exist');
    }
    
    /**
     * Tests generic return value
     *
     * @see     xp://net.xp_framework.unittest.reflection.TestClass#getMap
     */
    #[@test]
    public function genericReturnValue() {
      $this->assertEquals('array<string, lang.Object>', $this->class->getMethod('getMap')->getReturnType());
    }
  }
?>
