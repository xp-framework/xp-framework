<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_framework.unittest.core.generics';

  uses(
    'unittest.TestCase',
    'net.xp_framework.unittest.core.generics.Lookup'
  );

  /**
   * TestCase for generic behaviour at runtime.
   *
   * @see   xp://net.xp_framework.unittest.core.generics.Lookup
   */
  class net·xp_framework·unittest·core·generics·ArrayTest extends TestCase {
  
    /**
     * Test put() and get() methods
     *
     */
    #[@test]
    public function primitiveStringArrayValue() {
      $l= create('new net.xp_framework.unittest.core.generics.Lookup<string, string[]>', array(
        'this' => array($this->name)
      ));
      $this->assertEquals(array($this->name), $l->get('this'));
    }

    /**
     * Test put() and get() methods
     *
     */
    #[@test]
    public function primitiveStringArrayKey() {
      $l= create('new net.xp_framework.unittest.core.generics.Lookup<string[], unittest.TestCase>');
      $l->put(array('this'), $this);
      $this->assertEquals($this, $l->get(array('this')));
    }

    /**
     * Test put() and get() methods
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function stringToArrayOfStringInvalid() {
      create('new net.xp_framework.unittest.core.generics.Lookup<string, string[]>')
        ->put('greeting', array('Hello', 'World', '!!!', 1))
      ;
    }

    /**
     * Test put() and get() methods
     *
     */
    #[@test]
    public function stringToArrayOfStringMultiple() {
      $l= create('new net.xp_framework.unittest.core.generics.Lookup<string, string[]>', array(
        'colors' => array('red', 'green', 'blue'),
        'names'  => array('PHP', 'Java', 'C#')
        
      ));
      $this->assertEquals(array('red', 'green', 'blue'), $l->get('colors'));
      $this->assertEquals(array('PHP', 'Java', 'C#'), $l->get('names'));
    }
 
     /**
     * Test put() and get() methods
     *
     */
    #[@test]
    public function arrayOfStringToStringMultiple() {
      $l= create('new net.xp_framework.unittest.core.generics.Lookup<string[], string>');
      $l->put(array('red', 'green', 'blue'), 'colors');
      $l->put(array('PHP', 'Java', 'C#'), 'names');
      $this->assertEquals('colors', $l->get(array('red', 'green', 'blue')));
      $this->assertEquals('names', $l->get(array('PHP', 'Java', 'C#')));
    }
 }
?>
