<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'net.xp_framework.unittest.core.generics.Lookup',
    'lang.types.String'
  );

  /**
   * TestCase for definition reflection
   *
   * @see   xp://net.xp_framework.unittest.core.generics.Lookup
   */
  class DefinitionReflectionTest extends TestCase {
    protected $fixture= NULL;
    
    /**
     * Creates fixture, a Lookup class
     *
     */  
    public function setUp() {
      $this->fixture= XPClass::forName('net.xp_framework.unittest.core.generics.Lookup');
    }
  
    /**
     * Test isGenericDefinition()
     *
     */
    #[@test]
    public function isAGenericDefinition() {
      $this->assertTrue($this->fixture->isGenericDefinition());
    }

    /**
     * Test isGenericDefinition()
     *
     */
    #[@test]
    public function isNotAGeneric() {
      $this->assertFalse($this->fixture->isGeneric());
    }

    /**
     * Test genericComponents()
     *
     */
    #[@test]
    public function components() {
      $this->assertEquals(array('K', 'V'), $this->fixture->genericComponents());
    }

    /**
     * Test newGenericType()
     *
     */
    #[@test]
    public function newGenericTypeIsGeneric() {
      $t= $this->fixture->newGenericType(array(
        XPClass::forName('lang.types.String'), 
        XPClass::forName('unittest.TestCase')
      ));
      $this->assertTrue($t->isGeneric());
    }

    /**
     * Test newGenericType()
     *
     */
    #[@test]
    public function newLookupWithStringAndTestCase() {
      $arguments= array(
        XPClass::forName('lang.types.String'), 
        XPClass::forName('unittest.TestCase')
      );
      $this->assertEquals(
        $arguments, 
        $this->fixture->newGenericType($arguments)->genericArguments()
      );
    }

    /**
     * Test newGenericType()
     *
     */
    #[@test]
    public function newLookupWithStringAndObject() {
      $arguments= array(
        XPClass::forName('lang.types.String'), 
        XPClass::forName('lang.Object')
      );
      $this->assertEquals(
        $arguments, 
        $this->fixture->newGenericType($arguments)->genericArguments()
      );
    }

    /**
     * Test classes created via newGenericType() and from an instance
     * instantiated via create() are equal.
     *
     */
    #[@test]
    public function classesFromReflectionAndCreateAreEqual() {
      $this->assertEquals(
        create('new net.xp_framework.unittest.core.generics.Lookup<String, TestCase>()')->getClass(),
        $this->fixture->newGenericType(array(
          XPClass::forName('lang.types.String'), 
          XPClass::forName('unittest.TestCase')
        ))
      );
    }

    /**
     * Test newGenericType()
     *
     */
    #[@test]
    public function classesCreatedWithDifferentTypesAreNotEqual() {
      $this->assertNotEquals(
        $this->fixture->newGenericType(array(
          XPClass::forName('lang.types.String'), 
          XPClass::forName('lang.Object')
        )),
        $this->fixture->newGenericType(array(
          XPClass::forName('lang.types.String'), 
          XPClass::forName('unittest.TestCase')
        ))
      );
    }

    /**
     * Test newGenericType()
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function missingArguments() {
      $this->fixture->newGenericType(array());
    }

    /**
     * Test newGenericType()
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function missingArgument() {
      $this->fixture->newGenericType(array($this->getClass()));
    }

    /**
     * Test newGenericType()
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function tooManyArguments() {
      $c= $this->getClass();
      $this->fixture->newGenericType(array($c, $c, $c));
    }
  }
?>
