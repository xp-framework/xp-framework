<?php

  /* This class is part of the XP framework
   *
   * $Id$
   */

  uses(
    'unittest.TestCase',
    'util.CompositeProperties'
  );

  /**
   * TestCase
   *
   * @see       ...
   * @purpose   TestCase for
   */
  class CompositePropertiesTest extends TestCase {

    /**
     * Test
     *
     */
    #[@test]
    public function createCompositeSingle() {
      $c= new CompositeProperties(new Properties(''));
      $this->assertEquals(1, $c->length());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function createCompositeDual() {
      $c= new CompositeProperties(new Properties(''), array(new Properties('')));
      $this->assertEquals(2, $c->length());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readStringUsesFirstProperties() {
      $c= new CompositeProperties(Properties::fromString('[section]
str="string..."'),
array(Properties::fromString('[section]
str="Another thing"')));

      $this->assertEquals('string...', $c->readString('section', 'str'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readStringUsesSecondPropertiesWhenFirstEmpty() {
      $c= new CompositeProperties(Properties::fromString('[section]
str="string..."'),
array(Properties::fromString('[section]
str2="Another thing"')));

      $this->assertEquals('Another thing', $c->readString('section', 'str2'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readStringReturnsDefaultOnNoOccurrance() {
      $c= new CompositeProperties(Properties::fromString('[section]
str="string..."'),
array(Properties::fromString('[section]
str2="Another thing"')));

      $this->assertEquals('Hello World', $c->readString('section', 'str3', 'Hello World'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readBooleanUsesFirst() {
      $c= new CompositeProperties(Properties::fromString('[section]
b1=true'),
array(Properties::fromString('[section]
b1=false')));

      $this->assertEquals(TRUE, $c->readBool('section', 'b1'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readBooleanUsesSecondIfFirstUnset() {
      $c= new CompositeProperties(Properties::fromString('[section]
b1=true'),
array(Properties::fromString('[section]
b2=false')));

      $this->assertEquals(FALSE, $c->readBool('section', 'b2'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readBooleanUsesDefaultOnNoOccurrance() {
      $c= new CompositeProperties(Properties::fromString('[section]
b1=true'),
array(Properties::fromString('[section]
b2=false')));

      $this->assertEquals('Hello.', $c->readBool('section', 'b3', 'Hello.'));
    }
  }
?>
