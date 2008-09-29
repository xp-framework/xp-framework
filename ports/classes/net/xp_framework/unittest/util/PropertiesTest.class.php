<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'util.Properties',
    'util.Hashmap'
  );

  /**
   * Testcase for util.Properties class.
   *
   * @see      xp://util.Properties
   * @purpose  Testcase
   */
  class PropertiesTest extends TestCase {
  
    /**
     * Test construction via fromFile() method for a non-existant file
     *
     */
    #[@test, @expect('io.IOException')]
    public function fromNonExistantFile() {
      Properties::fromFile(new File('@@does-not-exist.ini@@'));
    }

    /**
     * Test construction via fromFile() method for an existant file.
     * Relies on a file "example.ini" existing parallel to this class.
     *
     */
    #[@test]
    public function fromFile() {
      $p= Properties::fromFile($this
        ->getClass()
        ->getPackage()
        ->getResourceAsStream('example.ini')
      );
      $this->assertEquals('value', $p->readString('section', 'key'));
    }

    /**
     * Test exceptions are not thrown until first read
     *
     */
    #[@test]
    public function lazyRead() {
      $p= new Properties('@@does-not-exist.ini@@');
      
      // This cannot be done via @expect because it would also catch if an
      // exception was thrown from util.Properties' constructor. We explicitely
      // want the exception to be thrown later on
      try {
        $p->readString('section', 'key');
        $this->fail('Expected exception not thrown', NULL, 'io.IOException');
      } catch (IOException $expected) {
        xp::gc();
      }
    }
  
    /**
     * Test construction via fromString() when given an empty string
     *
     */
    #[@test]
    public function fromEmptyString() {
      Properties::fromString('');
    }
    
    /**
     * Test simple reading of values.
     *
     */
    #[@test]
    public function basicTest() {
      $p= Properties::fromString('
[section]
string="value1"
int=2
bool=0
      ');
      
      $this->assertEquals('value1', $p->readString('section', 'string'));
      $this->assertEquals(2, $p->readInteger('section', 'int'));
      $this->assertEquals(FALSE, $p->readBool('Section', 'bool'));
    }
    
    /**
     * Test string reading
     *
     */
    #[@test]
    public function readString() {
      $p= Properties::fromString('
[section]
string1=string
string2="string"
      ');
      
      $this->assertEquals('string', $p->readString('section', 'string1'));
      $this->assertEquals('string', $p->readString('section', 'string2'));
    }    
    
    /**
     * Test simple reading of arrays
     *
     */
    #[@test]
    public function readArray() {
      $p= Properties::fromString('
[section]
array="foo|bar|baz"
      ');
      $this->assertEquals(array('foo', 'bar', 'baz'), $p->readArray('section', 'array'));
    }

    /**
     * Test that an empty key (e.g. values="" or values=" ") will become an empty array.
     *
     */
    #[@test]
    public function readEmptyArray() {
      $p= Properties::fromString('
[section]
empty=""
spaces=" "
unquoted= 
      ');
      $this->assertEquals(array(), $p->readArray('section', 'empty'));
      $this->assertEquals(array(), $p->readArray('section', 'spaces'));
      $this->assertEquals(array(), $p->readArray('section', 'unquoted'));
    }
    
    /**
     * Test simple reading of hashes
     *
     */
    #[@test]
    public function readHash() {
      $p= Properties::fromString('
[section]
hash="foo:bar|bar:foo"
      ');
      $this->assertEquals(
        new Hashmap(array('foo' => 'bar', 'bar' => 'foo')),
        $p->readHash('section', 'hash')
      );
    }   
    
    /**
     * Test simple reading of range
     *
     */
    #[@test]
    public function readRange() {
      $p= Properties::fromString('
[section]
range="1..5"
      ');
      $this->assertEquals(
        range(1, 5),
        $p->readRange('section', 'range')
      );
    }
    
    /**
     * Test simple reading of integer
     *
     */
    #[@test]
    public function readInteger() {
      $p= Properties::fromString('
[section]
int1=1
int2=0
int3=-5
      ');
      $this->assertEquals(1, $p->readInteger('section', 'int1'));
      $this->assertEquals(0, $p->readInteger('section', 'int2'));
      $this->assertEquals(-5, $p->readInteger('section', 'int3'));
    }
    
    /**
     * Test simple reading of float
     *
     */
    #[@test]
    public function readFloat() {
      $p= Properties::fromString('
[section]
float1=1
float2=0
float3=0.5
float4=-5.0
      ');
      $this->assertEquals(1.0, $p->readFloat('section', 'float1'));
      $this->assertEquals(0.0, $p->readFloat('section', 'float2'));
      $this->assertEquals(0.5, $p->readFloat('section', 'float3'));
      $this->assertEquals(-5.0, $p->readFloat('section', 'float4'));
    }
    
    /**
     * Tests reading of a boolean
     *
     */
    #[@test]
    public function readBool() {
     $p= Properties::fromString('
[section]
bool1=1
bool2=yes
bool3=on
bool4=true
bool5=0
bool6=no
bool7=off
bool8=false
      ');
      $this->assertTrue($p->readBool('section', 'bool1'), '1');
      $this->assertTrue($p->readBool('section', 'bool2'), 'yes');
      $this->assertTrue($p->readBool('section', 'bool3'), 'on');
      $this->assertTrue($p->readBool('section', 'bool4'), 'true');
      $this->assertFalse($p->readBool('section', 'bool5'), '0');
      $this->assertFalse($p->readBool('section', 'bool6'), 'no');
      $this->assertFalse($p->readBool('section', 'bool7'), 'off');
      $this->assertFalse($p->readBool('section', 'bool8'), 'false');
    }
    
    /**
     * Test simple reading of section
     *
     */
    #[@test]
    public function hasSection() {
      $p= Properties::fromString('
[section]
foo=bar
      ');
      
      $this->assertTrue($p->hasSection('section'));
      $this->assertFalse($p->hasSection('nonexistant'));
    }

    /**
     * Test iterating over sections
     *
     */
    #[@test]
    public function iterateSections() {
     $p= Properties::fromString('
[section]
foo=bar

[next]
foo=bar

[empty]

[final]
foo=bar
      ');
      
      $this->assertEquals('section', $p->getFirstSection());
      $this->assertEquals('next', $p->getNextSection());
      $this->assertEquals('empty', $p->getNextSection());     
      $this->assertEquals('final', $p->getNextSection());
    }
  }
?>
