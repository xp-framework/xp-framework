<?php namespace net\xp_framework\unittest\util;

use util\Properties;
use io\streams\MemoryOutputStream;

class PropertiesStreamWriterTest extends \unittest\TestCase {
  protected $prop;

  public function setUp() {
    $this->prop= new Properties(null);
    $this->prop->create();
  }

  protected function assertStoredAsBytes($expected, Properties $prop) {
    $mos= new MemoryOutputStream();
    $prop->store($mos);

    $this->assertEquals($expected, rtrim($mos->getBytes()));
  }

  #[@test]
  public function write_a_simple_string_property() {
    $this->prop->writeString('foo', 'bar', 'baz');
    $this->assertStoredAsBytes('[foo]
bar="baz"', $this->prop);
  }

  #[@test]
  public function write_an_empty_properties_file() {
    $this->assertStoredAsBytes('', $this->prop);
  }

  #[@test]
  public function comments_will_not_be_preserved() {
    $this->prop->writeString('section', 'key', 'value');
    $this->prop->writeComment('section', 'A comment');
    $this->prop->writeString('section', 'after_comment', 'another value');

    $this->assertStoredAsBytes('[section]
key="value"

; A comment
after_comment="another value"', $this->prop);
  }

  #[@test]
  public function multiple_sections() {
    $this->prop->writeString('section1', 'string', 'value');
    $this->prop->writeString('section2', 'string', 'value');

    $this->assertStoredAsBytes('[section1]
string="value"

[section2]
string="value"', $this->prop);
  }

  #[@test]
  public function writing_types() {
    $this->prop->writeString('section', 'string', 'my value');
    $this->prop->writeInteger('section', 'integer', 25);
    $this->prop->writeBool('section', 'boolean', true);
    $this->prop->writeFloat('section', 'float', 0.5);
    $this->prop->writeArray('section', 'array', array('my value', 25, true, 0.5));
    $this->prop->writeHash('section', 'hash', array('my value' => 25, "true" => 0.5));

    $this->prop->writeSection('section2');

    $this->assertStoredAsBytes('[section]
string="my value"
integer=25
boolean="yes"
float=0.5
array="my value|25|1|0.5"
hash="my value:25|true:0.5"

[section2]', $this->prop);
  }
}