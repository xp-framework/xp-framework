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
   * TestCase for generic behaviour at runtime.
   *
   * @see   xp://net.xp_framework.unittest.core.generics.Lookup
   */
  class PrimitivesTest extends TestCase {
  
    /**
     * Test put() and get() methods with a primitive string as key
     *
     */
    #[@test]
    public function primitiveStringKey() {
      $l= create('new net.xp_framework.unittest.core.generics.Lookup<string, TestCase>', array(
        'this' => $this
      ));
      $this->assertEquals($this, $l->get('this'));
    }

    /**
     * Test put() and get() methods with a primitive string as key
     *
     */
    #[@test]
    public function primitiveStringValue() {
      $l= create('new net.xp_framework.unittest.core.generics.Lookup<TestCase, string>()');
      $l->put($this, 'this');
      $this->assertEquals('this', $l->get($this));
    }

    /**
     * Test put() does not accept another primitive
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function primitiveVerification() {
      $l= create('new net.xp_framework.unittest.core.generics.Lookup<string, TestCase>()');
      $l->put(1, $this);
    }

    /**
     * Test put() does not accept instance
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function instanceVerification() {
      $l= create('new net.xp_framework.unittest.core.generics.Lookup<string, TestCase>()');
      $l->put(new String('Hello'), $this);
    }

    /**
     * Test getClass()
     *
     */
    #[@test]
    public function nameOfClass() {
      $type= XPClass::forName('net.xp_framework.unittest.core.generics.Lookup')->newGenericType(array(
        Primitive::$STRING,
        XPClass::forName('unittest.TestCase')
      ));
      $this->assertEquals('net.xp_framework.unittest.core.generics.Lookup`2[string,unittest.TestCase]', $type->getName());
    }

    /**
     * Test genericArguments()
     *
     */
    #[@test]
    public function typeArguments() {
      $this->assertEquals(
        array(Primitive::$STRING, XPClass::forName('unittest.TestCase')),
        create('new net.xp_framework.unittest.core.generics.Lookup<string, TestCase>()')->getClass()->genericArguments()
      );
    }
  }
?>
