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
    $this->prop->store($mos);

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
}