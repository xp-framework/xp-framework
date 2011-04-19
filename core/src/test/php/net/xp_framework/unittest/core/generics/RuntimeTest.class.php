<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'net.xp_framework.unittest.core.generics';

  uses(
    'unittest.TestCase',
    'net.xp_framework.unittest.core.generics.Lookup',
    'lang.types.String',
    'lang.types.Integer'
  );

  /**
   * TestCase for generic behaviour at runtime.
   *
   * @see   xp://collections.Lookup
   */
  class net·xp_framework·unittest·core·generics·RuntimeTest extends TestCase {
    protected $fixture= NULL;
    
    /**
     * Creates fixture, a Lookup with String and TestCase as component
     * types.
     *
     */  
    public function setUp() {
      $this->fixture= create('new net.xp_framework.unittest.core.generics.Lookup<String, TestCase>()');
    }
  
    /**
     * Test put() method succeeds with correct types
     *
     */
    #[@test]
    public function putStringAndThis() {
      $this->fixture->put(new String($this->name), $this);
    }

    /**
     * Test put() and get() roundtrip
     *
     */
    #[@test]
    public function putAndGetRoundTrip() {
      $key= new String($this->name);
      $this->fixture->put($key, $this);
      $this->assertEquals($this, $this->fixture->get($key));
    }

    /**
     * Test put() method raises an error with incorrect key type
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function keyTypeIncorrect() {
      $this->fixture->put(new Integer(1), $this);
    }

    /**
     * Test put() method raises an error with incorrect v aluetype
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function valueTypeIncorrect() {
      $this->fixture->put(new String($this->name), new Object());
    }
  }
?>
