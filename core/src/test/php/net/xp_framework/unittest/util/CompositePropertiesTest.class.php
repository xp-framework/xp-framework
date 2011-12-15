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

    protected function fixture() {
      return new CompositeProperties(Properties::fromString('[section]
str="string..."
b1=true
arr1="foo|bar"
hash1="a:b|b:c"
int1=5
float1=0.5

[read]
key=value'),
array(Properties::fromString('[section]
str="Another thing"
str2="Another thing"
b1=false
b2=false
arr1="foo|bar|baz"
arr2="foo|bar|baz"
hash1="b:a|c:b"
hash2="b:null"
int1=10
int2=4
float1=1.1
float2=4.99999999

[secondsection]
foo=bar

[read]
key="This must not appear, as first has precedence"
anotherkey="is there, too"

[empty]
')));

      return $c;
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readStringUsesFirstProperties() {
      $this->assertEquals('string...', $this->fixture()->readString('section', 'str'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readStringUsesSecondPropertiesWhenFirstEmpty() {
      $this->assertEquals('Another thing', $this->fixture()->readString('section', 'str2'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readStringReturnsDefaultOnNoOccurrance() {
      $this->assertEquals('Hello World', $this->fixture()->readString('section', 'str3', 'Hello World'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readBooleanUsesFirst() {
      $this->assertEquals(TRUE, $this->fixture()->readBool('section', 'b1'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readBooleanUsesSecondIfFirstUnset() {
      $this->assertEquals(FALSE, $this->fixture()->readBool('section', 'b2'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readBooleanUsesDefaultOnNoOccurrance() {
      $this->assertEquals('Hello.', $this->fixture()->readBool('section', 'b3', 'Hello.'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readArrayUsesFirst() {
      $this->assertEquals(array('foo', 'bar'), $this->fixture()->readArray('section', 'arr1'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readArrayUsesSecondIfFirstUnset() {
      $this->assertEquals(array('foo', 'bar', 'baz'), $this->fixture()->readArray('section', 'arr2'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readArrayUsesDefaultOnNoOccurrance() {
      $this->assertEquals('Hello.', $this->fixture()->readArray('section', 'arr3', 'Hello.'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readHashUsesFirst() {
      $this->assertEquals(new Hashmap(array('a' => 'b', 'b' => 'c')), $this->fixture()->readHash('section', 'hash1'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readHashUsesSecondIfFirstUnset() {
      $this->assertEquals(new Hashmap(array('b' => 'null')), $this->fixture()->readHash('section', 'hash2'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readHashUsesDefaultOnNoOccurrance() {
      $this->assertEquals('Hello.', $this->fixture()->readHash('section', 'hash3', 'Hello.'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readIntegerUsesFirst() {
      $this->assertEquals(5, $this->fixture()->readInteger('section', 'int1'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readIntegerUsesSecondIfFirstUnset() {
      $this->assertEquals(4, $this->fixture()->readInteger('section', 'int2'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readIntegerUsesDefaultOnNoOccurrance() {
      $this->assertEquals('Hello.', $this->fixture()->readInteger('section', 'int3', 'Hello.'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readFloatUsesFirst() {
      $this->assertEquals(0.5, $this->fixture()->readFloat('section', 'float1'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readFloatUsesSecondIfFirstUnset() {
      $this->assertEquals(4.99999999, $this->fixture()->readFloat('section', 'float2'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readFloatUsesDefaultOnNoOccurrance() {
      $this->assertEquals('Hello.', $this->fixture()->readFloat('section', 'float3', 'Hello.'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readSection() {
      $this->assertEquals(
        array('key' => 'value', 'anotherkey' => 'is there, too'),
        $this->fixture()->readSection('read')
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readSectionThatDoesNotExistReturnsDefault() {
      $this->assertEquals(array('default' => 'value'), $this->fixture()->readSection('doesnotexist', array('default' => 'value')));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readEmptySectionOverridesDefault() {
      $this->assertEquals(array(), $this->fixture()->readSection('empty', array('default' => 'value')));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function sectionExists() {
      $this->assertEquals(TRUE, $this->fixture()->hasSection('section'));
      $this->assertEquals(TRUE, $this->fixture()->hasSection('secondsection'));
      $this->assertEquals(FALSE, $this->fixture()->hasSection('any'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function getFirstSection() {
      $fixture= $this->fixture();
      $this->assertEquals('section', $fixture->getFirstSection());
      return $fixture;
    }

    /**
     * Test
     *
     */
    #[@test]
    public function getNextSection() {
      $fixture= $this->getFirstSection();
      $this->assertEquals('read', $fixture->getNextSection());
      return $fixture;
    }

    /**
     * Test
     *
     */
    #[@test]
    public function getThirdSection() {
      $fixture= $this->getNextSection();
      $this->assertEquals('secondsection', $fixture->getNextSection());
      return $fixture;
    }

    /**
     * Test
     *
     */
    #[@test]
    public function nextSectionCannotBeCalledWithoutCallToFirstSection() {
      $this->assertEquals(NULL, $this->fixture()->getNextSection());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function getLastSectionReturnsNull() {
      $fixture= $this->getThirdSection();
      $this->assertEquals('empty', $fixture->getNextSection());
      $this->assertEquals(NULL, $fixture->getNextSection());
      return $fixture;
    }

    /**
     * Test
     *
     */
    #[@test]
    public function lastSectionReturnsNullForever() {
      $this->assertEquals(NULL, $this->getLastSectionReturnsNull()->getNextSection());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function firstSectionResetsLoop() {
      $fixture= $this->getThirdSection();
      $this->assertEquals('section', $fixture->getFirstSection());
      $this->assertEquals('read', $fixture->getNextSection());
    }
  }
?>
