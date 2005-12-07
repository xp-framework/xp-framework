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
    function setUp() {
      $cl= &ClassLoader::getDefault();
      
      // For singletonInstance test
      $cl->defineClass(
        'net.xp_framework.unittest.core.AnonymousSingleton', 
        'class AnonymousSingleton extends Object {
           var $identifier= "";

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
           var $identifier= "";

           function __construct() {
             ReferencesTest::registry("list", $this);
           }
        }'
      );
      $cl->defineClass(
        'net.xp_framework.unittest.core.AnonymousFactory', 
        'class AnonymousFactory extends Object {
          function &factory() {
            return new AnonymousList();
          }
        }'
      );
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
    function singletonInstance() {
      $s1= &AnonymousSingleton::getInstance();
      $s2= &AnonymousSingleton::getInstance();
      $this->assertEquals($s1->identifier, $s2->identifier);
      $s1->identifier= 'singleton';
      $this->assertEquals($s1->identifier, $s2->identifier);
    }
    
    /**
     * Simulates static class member
     * 
     * @access  protected
     * @param   string key
     * @param   &mixed val
     * @return  &mixed
     */
    function &registry($key, &$val) {
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
    function returnNewObject() {
      $object= &AnonymousFactory::factory();
      $value= &ReferencesTest::registry('list', $r= NULL);
      
      $this->assertEquals($object->identifier, $value->identifier);
      $object->identifier= 'reference';
      $this->assertEquals($object->identifier, $value->identifier);
    }    

    /**
     * Tests "return new XXX()" still works.
     *
     * @access  public
     */
    #[@test]
    function returnNewObjectViaReflection() {
      $class= &XPClass::forName('net.xp_framework.unittest.core.AnonymousFactory');
      $factory= &$class->getMethod('factory');
      $object= &$factory->invoke($instance= NULL);
      $value= &ReferencesTest::registry('list', $r= NULL);
      
      $this->assertEquals($object->identifier, $value->identifier);
      $object->identifier= 'reference';
      $this->assertEquals($object->identifier, $value->identifier);
    }    
  }
?>
