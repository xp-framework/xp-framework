<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase');

  /**
   * References test.
   *
   * @purpose  Testcase
   */
  class ReferencesTest extends TestCase {

    static function __static() {
      
      // For singletonInstance test
      ClassLoader::defineClass('net.xp_framework.unittest.core.AnonymousSingleton', 'lang.Object', array(), '{
        protected static $instance= NULL;

        static function getInstance() {
          if (!isset(self::$instance)) self::$instance= new AnonymousSingleton();
          return self::$instance;
        }
      }');

      // For returnNewObject and returnNewObjectViaReflection tests
      ClassLoader::defineClass('net.xp_framework.unittest.core.AnonymousList', 'lang.Object', array(), '{
        function __construct() {
          ReferencesTest::registry("list", $this);
        }
      }');
      ClassLoader::defineClass('net.xp_framework.unittest.core.AnonymousFactory', 'lang.Object', array(), '{
        static function factory() {
          return new AnonymousList();
        }
      }');
      ClassLoader::defineClass('net.xp_framework.unittest.core.AnonymousNewInstanceFactory', 'lang.Object', array(), '{
        static function factory() {
          return XPClass::forName("net.xp_framework.unittest.core.AnonymousList")->newInstance();
        }
      }');
    }

    /**
     * Helper method that asserts to objects are references to each other
     *
     * @param   &lang.Object a
     * @param   &lang.Object b
     * @throws  unittest.AssertionFailedError
     */
    protected function assertReference($a, $b) {
      $this->assertEquals($a->__id, $b->__id);
      $a->__id= 'R:'.$a->__id;
      $this->assertEquals($a->__id, $b->__id);
    }

    /**
     * Tests singleton behaviour. The AnonymousSingleton::getInstance() 
     * method is the most interesting part here:
     * <ul>
     *   <li>The static instance variable needs to be assigned a *copy* of
     *       the new object
     *   </li>
     *   <li>The getInstance() method needs to return by reference, which
     *       needs to be assigned to by reference in the calling code
     *   </li>
     * </ul>
     *
     */
    #[@test]
    public function singletonInstance() {
      $s1= AnonymousSingleton::getInstance();
      $s2= AnonymousSingleton::getInstance();
      
      $this->assertReference($s1, $s2);
    }
    
    /**
     * Simulates static class member
     * 
     * @param   string key
     * @param   &mixed val
     * @return  &mixed
     */
    public static function registry($key, $val) {
      static $registry= array();
      
      if (NULL !== $val) $registry[$key]= $val;
      return $registry[$key];
    }
    
    /**
     * Tests "return new XXX()" still works.
     *
     */
    #[@test]
    public function returnNewObject() {
      $object= AnonymousFactory::factory();
      $value= ReferencesTest::registry('list', $r= NULL);
      
      $this->assertReference($object, $value);
    }    

    /**
     * Tests "return $method->invoke()" still works.
     *
     */
    #[@test]
    public function returnNewObjectViaMethodInvoke() {
      $class= XPClass::forName('net.xp_framework.unittest.core.AnonymousFactory');
      $factory= $class->getMethod('factory');
      $object= $factory->invoke($instance= NULL);
      $value= ReferencesTest::registry('list', $r= NULL);

      $this->assertReference($object, $value);
    }
    
    /**
     * Tests "return $class->newInstance()" still works.
     *
     */
    #[@test]
    public function returnNewObjectViaNewInstance() {
      $object= AnonymousNewInstanceFactory::factory();
      $value= ReferencesTest::registry('list', $r= NULL);
      
      $this->assertReference($object, $value);
    }
  }
?>
