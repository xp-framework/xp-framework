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
   */
  abstract class AbstractPropertiesTest extends TestCase {
  
    /**
     * Create a new properties object from a string source
     *
     * @param   string source
     * @return  util.Properties
     */
    protected abstract function newPropertiesFrom($source);


    /**
     * Test construction via fromString() when given an empty string
     *
     */
    #[@test]
    public function fromEmptySource() {
      $this->newPropertiesFrom('');
    }
  
    /**
     * Test simple reading of values.
     *
     */
    #[@test]
    public function basicTest() {
      $p= $this->newPropertiesFrom('
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
     * Test reading values with comments
     *
     */
    #[@test]
    public function valuesCommented() {
      $p= $this->newPropertiesFrom('
[section]
notQuotedComment=value1  ; A comment
quotedComment="value1"  ; A comment
quotedWithComment="value1 ; With comment"
      ');
      
      $this->assertEquals('value1', $p->readString('section', 'notQuotedComment'), 'not-quoted-with-comment');
      $this->assertEquals('value1', $p->readString('section', 'quotedComment'), 'quoted-comment');
      $this->assertEquals('value1 ; With comment', $p->readString('section', 'quotedWithComment'), 'quoted-with-comment');
    }
    
    /**
     * Test reading values are trimmed
     *
     */
    #[@test]
    public function valuesTrimmed() {
      $p= $this->newPropertiesFrom('
[section]
trim=  value1  
      ');
      
      $this->assertEquals('value1', $p->readString('section', 'trim'));
    }
    
    /**
     * Test reading quoted values, which are not trimmed
     *
     */
    #[@test]
    public function valuesQuoted() {
      $p= $this->newPropertiesFrom('
[section]
quoted="  value1  "
      ');
      
      $this->assertEquals('  value1  ', $p->readString('section', 'quoted'));
    }
    
    /**
     * Test string reading
     *
     */
    #[@test]
    public function readString() {
      $p= $this->newPropertiesFrom('
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
      $p= $this->newPropertiesFrom('
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
      $p= $this->newPropertiesFrom('
[section]
empty=""
spaces=" "
unquoted= 
      ');
      $this->assertEquals(array(), $p->readArray('section', 'empty'));
      $this->assertEquals(array(' '), $p->readArray('section', 'spaces'));
      $this->assertEquals(array(), $p->readArray('section', 'unquoted'));
    }
    
    /**
     * Test simple reading of hashes
     *
     */
    #[@test]
    public function readHash() {
      $p= $this->newPropertiesFrom('
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
      $p= $this->newPropertiesFrom('
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
      $p= $this->newPropertiesFrom('
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
      $p= $this->newPropertiesFrom('
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
     $p= $this->newPropertiesFrom('
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
      $p= $this->newPropertiesFrom('
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
     $p= $this->newPropertiesFrom('
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

    /**
     * Test keys with a array keys
     *
     */
    #[@test]
    public function arrayKeys() {
     $p= $this->newPropertiesFrom('
[section]
class[0]=util.Properties
class[1]=util.PropertyManager
      ');
      
      $this->assertEquals(
        array('class' => array('util.Properties', 'util.PropertyManager')),
        $p->readSection('section')
      );
    }

    /**
     * Test keys with array keys
     *
     */
    #[@test]
    public function arrayKeysEmptyOffset() {
     $p= $this->newPropertiesFrom('
[section]
class[]=util.Properties
class[]=util.PropertyManager
      ');
      
      $this->assertEquals(
        array('class' => array('util.Properties', 'util.PropertyManager')),
        $p->readSection('section')
      );
    }

    /**
     * Test keys with array keys
     *
     */
    #[@test]
    public function readArrayFromArrayKeys() {
     $p= $this->newPropertiesFrom('
[section]
class[]=util.Properties
class[]=util.PropertyManager
      ');
      
      $this->assertEquals(
        array('util.Properties', 'util.PropertyManager'),
        $p->readArray('section', 'class')
      );
    }

    /**
     * Test keys with a hash keys
     *
     */
    #[@test]
    public function hashKeys() {
     $p= $this->newPropertiesFrom('
[section]
class[one]=util.Properties
class[two]=util.PropertyManager
      ');
      
      $this->assertEquals(
        array('class' => array('one' => 'util.Properties', 'two' => 'util.PropertyManager')),
        $p->readSection('section')
      );
    }

    /**
     * Test keys with a hash keys
     *
     */
    #[@test]
    public function readHashFromHashKeys() {
     $p= $this->newPropertiesFrom('
[section]
class[one]=util.Properties
class[two]=util.PropertyManager
      ');
      
      $this->assertEquals(
        new Hashmap(array('one' => 'util.Properties', 'two' => 'util.PropertyManager')),
        $p->readHash('section', 'class')
      );
    }
    
    /**
     * Test multiline value
     *
     */
    #[@test]
    public function verifyMultilineValuesEquals() {
      $p= $this->newPropertiesFrom('
[section]
key="
first line
second line
third line"
      ');
      $expected= '
first line
second line
third line';

      $this->assertEquals($expected, $p->readString('section', 'key'));
    }
  }
?>
