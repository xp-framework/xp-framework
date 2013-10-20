<?php namespace net\xp_framework\unittest\util;


use unittest\TestCase;
use util\CompositeProperties;


/**
 * Test CompositeProperties
 *
 * @see       xp://util.CompositeProperies
 */
class CompositePropertiesTest extends TestCase {

  /**
   * Test
   *
   */
  #[@test]
  public function createCompositeSingle() {
    $c= new CompositeProperties(array(new \util\Properties('')));
    $this->assertEquals(1, $c->length());
  }

  /**
   * Test
   *
   */
  #[@test]
  public function createCompositeDual() {
    $c= new CompositeProperties(array(new \util\Properties(''), new \util\Properties('')));
    $this->assertEquals(2, $c->length());
  }

  /**
   * Test
   *
   */
  #[@test, @expect('lang.IllegalArgumentException')]
  public function createCompositeThrowsExceptionWhenNoArgumentGiven() {
    new CompositeProperties();
  }

  /**
   * Test
   *
   */
  #[@test, @expect('lang.IllegalArgumentException')]
  public function createEmptyCompositeThrowsException() {
    new CompositeProperties(array());
  }

  /**
   * Test
   *
   */
  #[@test, @expect('lang.IllegalArgumentException')]
  public function createCompositeThrowsExceptionWhenSomethingElseThenPropertiesGiven() {
    new CompositeProperties(array(new \util\Properties(null), 1, new \util\Properties(null)));
  }

  /**
   * Test
   *
   */
  #[@test]
  public function addOtherProperties() {
    $c= new CompositeProperties(array(new \util\Properties(null)));
    $this->assertEquals(1, $c->length());

    $c->add(\util\Properties::fromString('[section]'));
    $this->assertEquals(2, $c->length());
  }

  /**
   * Test
   *
   */
  #[@test]
  public function addingIdenticalPropertiesIsIdempotent() {
    $p= new \util\Properties('');
    $c= new CompositeProperties(array($p));
    $this->assertEquals(1, $c->length());

    $c->add($p);
    $this->assertEquals(1, $c->length());
  }

  /**
   * Test
   *
   */
  #[@test]
  public function addingEqualPropertiesIsIdempotent() {
    $c= new CompositeProperties(array(\util\Properties::fromString('[section]
a=b
b=c')));
    $this->assertEquals(1, $c->length());

    $c->add(\util\Properties::fromString('[section]
a=b
b=c'));
    $this->assertEquals(1, $c->length());
  }

  protected function fixture() {
    return new CompositeProperties(array(\util\Properties::fromString('[section]
str="string..."
b1=true
arr1="foo|bar"
arr3="foo"
hash1="a:b|b:c"
int1=5
float1=0.5
range1=1..3

[read]
key=value'),
      \util\Properties::fromString('[section]
str="Another thing"
str2="Another thing"
b1=false
b2=false
arr1="foo|bar|baz"
arr2="foo|bar|baz"
arr3[]="bar"
hash1="b:a|c:b"
hash2="b:null"
int1=10
int2=4
float1=1.1
float2=4.99999999
range1=5..6
range2=99..100

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
    $this->assertEquals('Hello World', $this->fixture()->readString('section', 'non-existant-key', 'Hello World'));
  }

  /**
   * Test
   *
   * @see   https://github.com/xp-framework/xp-framework/issues/302
   */
  #[@test]
  public function readStringDefaultForDefault() {
    $this->assertEquals('', $this->fixture()->readString('section', 'non-existant-key'));
  }

  /**
   * Test
   *
   */
  #[@test]
  public function readBooleanUsesFirst() {
    $this->assertEquals(true, $this->fixture()->readBool('section', 'b1'));
  }

  /**
   * Test
   *
   */
  #[@test]
  public function readBooleanUsesSecondIfFirstUnset() {
    $this->assertEquals(false, $this->fixture()->readBool('section', 'b2'));
  }

  /**
   * Test
   *
   */
  #[@test]
  public function readBooleanUsesDefaultOnNoOccurrance() {
    $this->assertEquals(true, $this->fixture()->readBool('section', 'non-existant-key', true));
  }

  /**
   * Test
   *
   */
  #[@test]
  public function readBooleanUsesFalseForDefaultOnNoOccurrance() {
    $this->assertEquals(false, $this->fixture()->readBool('section', 'b3'));
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
    $this->assertEquals(array(1, 2, 3), $this->fixture()->readArray('section', 'non-existant-key', array(1, 2, 3)));
  }

  /**
   * Test
   *
   */
  #[@test]
  public function readArrayUsesEmptyArrayDefaultOnNoOccurrance() {
    $this->assertEquals(array(), $this->fixture()->readArray('section', 'non-existant-key'));
  }

  /**
   * Test
   *
   */
  #[@test]
  public function readArrayDoesNotAddArrayElements() {
    $this->assertEquals(array('foo'), $this->fixture()->readArray('section', 'arr3'));
  }

  /**
   * Test
   *
   */
  #[@test]
  public function readHashUsesFirst() {
    $this->assertEquals(new \util\Hashmap(array('a' => 'b', 'b' => 'c')), $this->fixture()->readHash('section', 'hash1'));
  }

  /**
   * Test
   *
   */
  #[@test]
  public function readHashUsesSecondIfFirstUnset() {
    $this->assertEquals(new \util\Hashmap(array('b' => 'null')), $this->fixture()->readHash('section', 'hash2'));
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
  public function readHashUsesNullForDefaultOnNoOccurrance() {
    $this->assertEquals(null, $this->fixture()->readHash('section', 'hash3'));
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
    $this->assertEquals(-1, $this->fixture()->readInteger('section', 'non-existant-key', -1));
  }

  /**
   * Test
   *
   */
  #[@test]
  public function readIntegerUsesZeroForDefaultOnNoOccurrance() {
    $this->assertEquals(0, $this->fixture()->readInteger('section', 'non-existant-key'));
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
    $this->assertEquals(-1.0, $this->fixture()->readFloat('section', 'non-existant-key', -1.0));
  }

  /**
   * Test
   *
   */
  #[@test]
  public function readFloatUsesZeroDefaultOnNoOccurrance() {
    $this->assertEquals(0.0, $this->fixture()->readFloat('section', 'non-existant-key'));
  }

  /**
   * Test
   *
   */
  #[@test]
  public function readRangeUsesFirst() {
    $this->assertEquals(array(1, 2, 3), $this->fixture()->readRange('section', 'range1'));
  }

  /**
   * Test
   *
   */
  #[@test]
  public function readRangeUsesSecondIfFirstUnset() {
    $this->assertEquals(array(99, 100), $this->fixture()->readRange('section', 'range2'));
  }

  /**
   * Test
   *
   */
  #[@test]
  public function readRangeUsesDefaultOnNoOccurrance() {
    $this->assertEquals(array(1, 2, 3), $this->fixture()->readRange('section', 'non-existant-key', array(1, 2, 3)));
  }

  /**
   * Test
   *
   */
  #[@test]
  public function readRangeUsesEmptyArrayForDefaultOnNoOccurrance() {
    $this->assertEquals(array(), $this->fixture()->readRange('section', 'non-existant-key'));
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
  public function readSectionThatDoesNotExistReturnsEmptyArrayPerDefault() {
    $this->assertEquals(array(), $this->fixture()->readSection('doesnotexist'));
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
  public function sectionFromMultipleSourcesExists() {
    $this->assertEquals(true, $this->fixture()->hasSection('section'));
  }

  /**
   * Test
   *
   */
  #[@test]
  public function sectionFromSingleSourceExists() {
    $this->assertEquals(true, $this->fixture()->hasSection('secondsection'));
  }

  /**
   * Test
   *
   */
  #[@test]
  public function nonexistantSectionDoesNotExist() {
    $this->assertEquals(false, $this->fixture()->hasSection('any'));
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
    $this->assertEquals(null, $this->fixture()->getNextSection());
  }

  /**
   * Test
   *
   */
  #[@test]
  public function getLastSectionReturnsNull() {
    $fixture= $this->getThirdSection();
    $this->assertEquals('empty', $fixture->getNextSection());
    $this->assertEquals(null, $fixture->getNextSection());
    return $fixture;
  }

  /**
   * Test
   *
   */
  #[@test]
  public function lastSectionReturnsNullForever() {
    $this->assertEquals(null, $this->getLastSectionReturnsNull()->getNextSection());
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

  /**
   * Test
   *
   */
  #[@test]
  public function addingToCompositeResetsIterationPointer() {
    $fixture= $this->getThirdSection();
    $fixture->add(\util\Properties::fromString('[unknown]'));

    $this->assertEquals(null, $fixture->getNextSection());
  }
}
