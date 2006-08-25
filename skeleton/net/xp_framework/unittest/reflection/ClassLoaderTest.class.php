<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.profiling.unittest.TestCase');

  /**
   * Test the XP default classloader
   *
   * @see      xp://lang.ClassLoader
   * @purpose  Testcase
   */
  class ClassLoaderTest extends TestCase {
    var
      $classLoader= NULL;
    
    /**
     * Helper method
     *
     * @access  protected
     * @param   string name
     * @param   &lang.XPClass class
     * @return  bool
     * @throws  util.profiling.unittest.AssertionFailedError
     */
    function assertXPClass($name, &$class) {
      return (
        $this->assertClass($class, 'lang.XPClass') &&
        $this->assertEquals($name, $class->getName()) &&
        $this->assertEquals('lang.XPClass<'.$name.'>', $class->toString())
      );
    }
  
    /**
     * Setup method
     *
     * @access  public
     */
    function setUp() {
      $this->classLoader= &ClassLoader::getDefault();
      $this->assertXPClass('lang.ClassLoader', $this->classLoader->getClass());
    }
 
    /**
     * Loads a class that has been loaded before
     *
     * @access  public
     */
    #[@test]
    function loadClass() {
      $this->assertXPClass('lang.Object', $this->classLoader->loadClass('lang.Object'));
    }

    /**
     * Tests the findClass() method
     *
     * @access  public
     */
    #[@test]
    function findThisClass() {
      $this->assertEquals(realpath(__FILE__), $this->classLoader->findClass($this->getClassName()));
    }

    /**
     * Tests the findClass() method
     *
     * @access  public
     */
    #[@test]
    function findNullClass() {
      $this->assertFalse($this->classLoader->findClass(NULL));
    }

    /**
     * Loads a class that has *not* been loaded before. Makes sure the
     * static initializer is called.
     *
     * @access  public
     */
    #[@test]
    function initializerCalled() {
      $name= 'net.xp_framework.unittest.reflection.LoaderTestClass';
      if (class_exists(xp::reflect($name))) {
        return $this->fail('Class "'.$name.'" may not exist!');
      }

      $class= &$this->classLoader->loadClass($name);
      $this->assertXPClass($name, $class);
      $this->assertTrue(LoaderTestClass::initializerCalled());
    }

    /**
     * Tests the loadClass() method throws a ClassNotFoundException when given
     * a name of a class that cannot be found. 
     *
     * @access  public
     */
    #[@test, @expect('lang.ClassNotFoundException')]
    function loadNonExistantClass() {
      $this->classLoader->loadClass('@@NON-EXISTANT@@');
    }

    /**
     * Tests the defineClass() method
     *
     * @access  public
     */
    #[@test]
    function defineClass() {
      $name= 'net.xp_framework.unittest.reflection.RuntimeDefinedClass';
      if (class_exists(xp::reflect($name))) {
        return $this->fail('Class "'.$name.'" may not exist!');
      }
      
      $class= &$this->classLoader->defineClass($name, 'class RuntimeDefinedClass extends Object {
        function __static() { RuntimeDefinedClass::initializerCalled(TRUE); }
        function initializerCalled($value= NULL) { 
          static $called; 
          if (NULL !== $value) $called= $value;
          return $called;
        }
      }');
      $this->assertXPClass($name, $class);
      $this->assertTrue(RuntimeDefinedClass::initializerCalled());
    }
    
    /**
     * Tests defineClass() with a given interface
     *
     * @access  public
     */
    #[@test]
    function defineClassImplements() {
      $name= 'net.xp_framework.unittest.reflection.RuntimeDefinedClassWithInterface';
      $class= &$this->classLoader->defineClass(
        $name, 
        'lang.Object',
        array('util.log.Traceable'),
        '{ function setTrace(&$cat) { } }'
      );

      $this->assertTrue(is('util.log.Traceable', $class->newInstance()));
      $this->assertFalse(is('util.log.Observer', $class->newInstance()));
    }
     
    
    /**
     * Tests the defineClass() method for the situtation where the bytes 
     * argument failed to actually declare the class.
     *
     * @access  public
     */
    #[@test, @expect('lang.FormatException')]
    function defineIllegalClass() {
      $name= 'net.xp_framework.unittest.reflection.IllegalClass';
      if (class_exists(xp::reflect($name))) {
        return $this->fail('Class "'.$name.'" may not exist!');
      }
      $this->classLoader->defineClass($name, '1;');
    }
  }
?>
