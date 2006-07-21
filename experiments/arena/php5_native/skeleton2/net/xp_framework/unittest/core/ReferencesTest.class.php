<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.profiling.unittest.TestCase');

  /**
   * References test.
   *
   * @purpose  Testcase
   */
  class ReferencesTest extends TestCase {

    /**
     * Setup method
     *
     * @access  public
     */
    public function setUp() {
      $cl= &ClassLoader::getDefault();
      
      // For singletonInstance test
      $cl->defineClass(
        'net.xp_framework.unittest.core.AnonymousSingleton', 
        'class AnonymousSingleton extends Object {
           function &getInstance() {
             static $instance= NULL;
             
             if (!isset($instance)) $instance= new AnonymousSingleton();
             return $instance;
           }
        }'
      );

      // For returnNewObject and returnNewObjectViaReflection tests
      $cl->defineClass(
        'net.xp_framework.unittest.core.AnonymousList', 
        'class AnonymousList extends Object {
           function __construct() {
             ReferencesTest::registry("list", $this);
           }
        }'
      );
      $cl->defineClass(
        'net.xp_framework.unittest.core.AnonymousFactory', 
        'class AnonymousFactory extends Object {
          function &factory() {
            $list= &new AnonymousList();
            return $list;
          }
        }'
      );
      $cl->defineClass(
        'net.xp_framework.unittest.core.AnonymousNewInstanceFactory', 
        'class AnonymousNewInstanceFactory extends Object {
          function &factory() {
            $class= &XPClass::forName("net.xp_framework.unittest.core.AnonymousList");
            return $class->newInstance();
          }
        }'
      );
    }

    /**
     * Helper method that asserts to objects are references to each other
     *
     * @access  protected
     * @param   &lang.Object a
     * @param   &lang.Object b
     * @throws  util.profiling.unittest.AssertionFailedError
     */
    public function assertReference(&$a, &$b) {
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
     * @access  public
     */
    #[@test]
    public function singletonInstance() {
      $s1= &AnonymousSingleton::getInstance();
      $s2= &AnonymousSingleton::getInstance();
      
      $this->assertReference($s1, $s2);
    }
    
    /**
     * Simulates static class member
     * 
     * @access  protected
     * @param   string key
     * @param   &mixed val
     * @return  &mixed
     */
    public function &registry($key, &$val) {
      static $registry= array();
      
      if (NULL !== $val) $registry[$key]= &$val;
      return $registry[$key];
    }
    
    /**
     * Tests "return new XXX()" still works.
     *
     * @access  public
     */
    #[@test]
    public function returnNewObject() {
      $object= &AnonymousFactory::factory();
      $value= &ReferencesTest::registry('list', $r= NULL);
      
      $this->assertReference($object, $value);
    }    

    /**
     * Tests "return $method->invoke()" still works.
     *
     * @access  public
     */
    #[@test]
    public function returnNewObjectViaMethodInvoke() {
      $class= &XPClass::forName('net.xp_framework.unittest.core.AnonymousFactory');
      $factory= &$class->getMethod('factory');
      $object= &$factory->invoke($instance= NULL);
      $value= &ReferencesTest::registry('list', $r= NULL);

      $this->assertReference($object, $value);
    }
    
    /**
     * Tests "return $class->newInstance()" still works.
     *
     * @access  public
     */
    #[@test]
    public function returnNewObjectViaNewInstance() {
      $object= &AnonymousNewInstanceFactory::factory();
      $value= &ReferencesTest::registry('list', $r= NULL);
      
      $this->assertReference($object, $value);
    }
  }
?>
