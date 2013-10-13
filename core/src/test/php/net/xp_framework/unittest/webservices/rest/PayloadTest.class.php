<?php namespace net\xp_framework\unittest\webservices\rest;



use unittest\TestCase;
use webservices\rest\Payload;


/**
 * Test payload class
 *
 * @see  xp://webservices.rest.Payload
 */
class PayloadTest extends TestCase {

  /**
   * Test constructor
   * 
   */
  #[@test]
  public function create() {
    new Payload();
  }

  /**
   * Test value
   * 
   */
  #[@test]
  public function value() {
    $value= array('key' => 'value');
    $this->assertEquals($value, create(new Payload($value))->value);
  }

  /**
   * Test properties
   * 
   */
  #[@test]
  public function properties() {
    $properties= array('key' => 'value');
    $this->assertEquals($properties, create(new Payload(null, $properties))->properties);
  }

  /**
   * Test equals()
   * 
   */
  #[@test]
  public function null_payloads_are_equal() {
    $this->assertEquals(new Payload(null), new Payload(null));
  }

  /**
   * Test equals()
   * 
   */
  #[@test]
  public function null_and_object_payloads_are_equal() {
    $this->assertNotEquals(new Payload($this), new Payload(null));
  }

  /**
   * Test equals()
   * 
   */
  #[@test]
  public function object_payloads_are_equal() {
    $this->assertEquals(new Payload(new \lang\types\String('a')), new Payload(new \lang\types\String('a')));
  }

  /**
   * Test equals()
   * 
   */
  #[@test]
  public function array_of_object_payloads_are_equal() {
    $this->assertEquals(new Payload(array(new \lang\types\String('a'))), new Payload(array(new \lang\types\String('a'))));
  }

  /**
   * Test equals()
   * 
   */
  #[@test]
  public function identical_object_payloads_are_equal() {
    $this->assertEquals(new Payload($this), new Payload($this));
  }

  /**
   * Test equals()
   * 
   */
  #[@test]
  public function different_object_payloads_are_not_equal() {
    $this->assertNotEquals(new Payload($this), new Payload(new \lang\Object()));
  }

  /**
   * Test equals()
   * 
   */
  #[@test]
  public function primitive_payloads_are_equal() {
    $this->assertEquals(new Payload('test'), new Payload('test'));
  }

  /**
   * Test equals()
   * 
   */
  #[@test]
  public function different_primitive_payloads_are_not_equal() {
    $this->assertNotEquals(new Payload('test1'), new Payload('test2'));
  }

  /**
   * Test equals()
   * 
   */
  #[@test]
  public function map_payloads_are_equal() {
    $this->assertEquals(new Payload(array('key' => 'value')), new Payload(array('key' => 'value')));
  }

  /**
   * Test equals()
   * 
   */
  #[@test]
  public function different_map_payloads_are_not_equal() {
    $this->assertNotEquals(new Payload(array('key' => 'value')), new Payload(array('test' => 'yes')));
  }

  /**
   * Test equals()
   *
   */
  #[@test]
  public function properties_are_equal() {
    $this->assertEquals(
      new Payload(null, array('key' => 'value')),
      new Payload(null, array('key' => 'value'))
    );
  }

  /**
   * Test equals()
   *
   */
  #[@test]
  public function different_properties_are_not_equal() {
    $this->assertNotEquals(
      new Payload(null, array('key' => 'value')),
      new Payload(null, array('test' => 'yes'))
    );
  }
}
