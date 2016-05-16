<?php namespace net\xp_framework\unittest\util;

use io\streams\MemoryInputStream;
use unittest\TestCase;
use util\ResolvingProperties;
use util\Properties;
use unittest\mock\MockRepository;

/**
 * Test for ResolvingProperties
 *
 * @see xp://util.ResolvingProperties
 */
class ResolvingPropertiesTest extends TestCase {

  /**
   * Creates a mock property manager
   *
   * @return  util.PropertyManager
   */
  private function getMockPropertyManager() {
    $repo= new MockRepository();
    $pm= $repo->createMock('util.PropertyManager', false);
    return $pm;
  }

  /**
   * Test parsing of the "prop" token
   */
  #[@test]
  public function parsePropToken() {
    $pm= $this->getMockPropertyManager();

    $properties= new Properties(NULL);
    $properties->load(new MemoryInputStream('
      [childsection]
      childvalue=childresult
    '));
    $pm->register('child', new ResolvingProperties($properties, $pm));

    $properties= new Properties(NULL);
    $properties->load(new MemoryInputStream('
      [section]
      value=${prop.child.childsection.childvalue}
      value2=${prop.child.childsection.childvaluemissing|default}
    '));
    $pm->register('parent', new ResolvingProperties($properties, $pm));

    $properties= $pm->getProperties('parent');

    $this->assertEquals('childresult', $properties->readString('section', 'value'));
    $this->assertEquals('default', $properties->readString('section', 'value2'));
  }

  /**
   * Test parsing of the "prop" token failed because of of an missing properties
   */
  #[@test, @expect(class= 'lang.ElementNotFoundException', withMessage= 'Can\'t find properties')]
  public function parsePropTokenMissingProperty() {
    $pm= $this->getMockPropertyManager();

    $properties= new Properties(NULL);
    $properties->load(new MemoryInputStream('
      [section]
      value=${prop.is.missing.now}
    '));
    $pm->register('parent',  new ResolvingProperties($properties, $pm));

    $properties= $pm->getProperties('parent');
    $properties->readString('section', 'value');
  }

  /**
   * Test parsing of the "prop" token failed because of an of missing string
   */
  #[@test, @expect(class= 'lang.ElementNotFoundException', withMessage= 'Can\'t find string')]
  public function parsePropTokenMissingString() {
    $pm= $this->getMockPropertyManager();

    $properties= new Properties(NULL);
    $properties->load(new MemoryInputStream('
      [childsection]
      childvalue=childresult
    '));
    $pm->register('child', new ResolvingProperties($properties));

    $properties= new Properties(NULL);
    $properties->load(new MemoryInputStream('
      [section]
      value=${prop.child.childsection.childvaluemissing}
    '));
    $pm->register('parent', new ResolvingProperties($properties, $pm));

    $properties= $pm->getProperties('parent');
    $properties->readString('section', 'value');
  }

  /**
   * Test parsing of the "prop" token failed because of an invalid argument
   */
  #[@test, @expect(class= 'lang.FormatException', withMessage= 'Invalid arguments')]
  public function parsePropTokenInvalidArg() {
    $pm= $this->getMockPropertyManager();

    $properties= new Properties(NULL);
    $properties->load(new MemoryInputStream('
      [section]
      value=${prop.is.invalid}
    '));
    $pm->register('parent', new ResolvingProperties($properties, $pm));

    $properties= $pm->getProperties('parent');
    $properties->readString('section', 'value');
  }

  /**
   * Test parsing of the "env" token
   */
  #[@test]
  public function parseEnvToken() {
    putenv("PARSEPROPTOKENTEST=result");
    $pm= $this->getMockPropertyManager();

    $properties= new Properties(NULL);
    $properties->load(new MemoryInputStream('
      [section]
      value=${env.PARSEPROPTOKENTEST}
      value2=${env.MISSINGENVVAR|default}
    '));
    $pm->register('properties', new ResolvingProperties($properties, $pm));

    $properties= $pm->getProperties('properties');

    $this->assertEquals('result', $properties->readString('section', 'value'));
    $this->assertEquals('default', $properties->readString('section', 'value2'));
  }

  /**
   * Test parsing of the "env" token failed because of an missing env var
   */
  #[@test, @expect(class= 'lang.ElementNotFoundException', withMessage= 'Environment variable')]
  public function parseEnvTokenMissingEnv() {
    putenv("PARSEPROPTOKENTEST=result");
    $pm= $this->getMockPropertyManager();

    $properties= new Properties(NULL);
    $properties->load(new MemoryInputStream('
      [section]
      value=${env.MISSINGENVVAR}
    '));
    $pm->register('properties', new ResolvingProperties($properties, $pm));

    $properties= $pm->getProperties('properties');
    $properties->readString('section', 'value');
  }

}

