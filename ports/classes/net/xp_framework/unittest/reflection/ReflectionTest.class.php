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
     * Tests subclass
     *
     * @see     xp://lang.XPClass#isSubclassOf
     */
    #[@test]
    public function subClassOfClass() {
      $this->assertTrue($this->class->isSubclassOf(XPClass::forName('lang.Object')));
      $this->assertFalse($this->class->isSubclassOf(XPClass::forName('util.Date')));
      $this->assertFalse($this->class->isSubclassOf(XPClass::forName('net.xp_framework.unittest.reflection.TestClass')));
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
     * Tests this class has a constructor
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
     * Tests lang.Object class has no constructor
     *
     * @see     xp://lang.XPClass#hasConstructor
     */
    #[@test]
    public function checkNoConstructor() {
      $this->assertFalse(XPClass::forName('lang.Object')->hasConstructor());
    }

    /**
     * Tests lang.Object class has no constructor
     *
     * @see     xp://lang.XPClass#getConstructor
     */
    #[@test, @expect('lang.ElementNotFoundException')]
    public function noConstructor() {
      XPClass::forName('lang.Object')->getConstructor();
    }

    /**
     * Tests constructor incovation
     *
     * @see     xp://lang.reflect.Constructor#newInstance
     */
    #[@test]
    public function constructorInvocation() {
      $instance= $this->class->getConstructor()->newInstance(array('1977-12-14'));
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
     * Tests trying to instantiate an interface throws an exception
     *
     * @see     xp://lang.reflect.Constructor#newInstance
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function newInstanceForInterface() {
      XPClass::forName('util.log.Traceable')->newInstance();
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
     * Tests getAnnotation() throws an exception if the passed annotation
     * name does not exist.
     *
     * @see     xp://lang.XPClass#getAnnotation
     */
    #[@test, @expect('lang.ElementNotFoundException')]
    public function nonExistantAnnotation() {
      $this->class->getAnnotation('non-existant');
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
     * Tests getClasses()
     *
     * @see     xp://lang.XPClass#getClasses
     */
    #[@test]
    public function getClasses() {
      $classes= XPClass::getClasses();
      $this->assertArray($classes);
      foreach ($classes as $class) {
        $this->assertClass($class, 'lang.XPClass');
      }
    }
    
    /**
     * Retrieval of string, int & null constant
     *
     */
    #[@test]
    public function getConstantString() {
      $this->assertEquals(TRUE, $this->class->hasConstant('CONSTANT_STRING'));
      $this->assertEquals('XP Framework', $this->class->getConstant('CONSTANT_STRING'));
      $this->assertEquals(TRUE, $this->class->hasConstant('CONSTANT_INT'));
      $this->assertEquals(15, $this->class->getConstant('CONSTANT_INT'));
      $this->assertEquals(TRUE, $this->class->hasConstant('CONSTANT_NULL'));
      $this->assertEquals(NULL, $this->class->getConstant('CONSTANT_NULL'));
    }
    
    /**
     * Retrieval of nonexistant constant yields an exception
     *
     */
    #[@test, @expect('lang.ElementNotFoundException')]
    public function retrieveNonexistingConstant() {
      $this->class->getConstant('DOES_NOT_EXIST');
    }
  }
?>
