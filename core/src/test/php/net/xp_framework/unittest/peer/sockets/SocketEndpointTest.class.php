<?php namespace net\xp_framework\unittest\peer\sockets;

use unittest\TestCase;
use peer\SocketEndpoint;
use peer\net\Inet4Address;
use peer\net\Inet6Address;


/**
 * TestCase
 *
 * @see      xp://peer.SocketEndpoint
 */
class SocketEndpointTest extends TestCase {

  #[@test]
  public function v4_string_passed_to_constructor() {
    $this->assertEquals('127.0.0.1', create(new SocketEndpoint('127.0.0.1', 6100))->getHost());
  }

  #[@test]
  public function v4_addr_passed_to_constructor() {
    $this->assertEquals(
      '127.0.0.1',
      create(new SocketEndpoint(new Inet4Address('127.0.0.1'), 6100))->getHost()
    );
  }

  #[@test]
  public function v6_string_passed_to_constructor() {
    $this->assertEquals('fe80::1', create(new SocketEndpoint('fe80::1', 6100))->getHost());
  }

  #[@test]
  public function v6_addr_passed_to_constructor() {
    $this->assertEquals(
      'fe80::1',
      create(new SocketEndpoint(new Inet6Address('fe80::1'), 6100))->getHost()
    );
  }

  #[@test]
  public function port_passed_to_constructor() {
    $this->assertEquals(6100, create(new SocketEndpoint('127.0.0.1', 6100))->getPort());
  }

  #[@test]
  public function equal_to_same() {
    $this->assertEquals(
      new SocketEndpoint('127.0.0.1', 6100),
      new SocketEndpoint('127.0.0.1', 6100)
    );
  }

  #[@test]
  public function equal_to_itself() {
    $fixture= new SocketEndpoint('127.0.0.1', 6100);
    $this->assertEquals($fixture, $fixture);
  }

  #[@test]
  public function not_equal_to_this() {
    $this->assertNotEquals($this, new SocketEndpoint('127.0.0.1', 6100));
  }

  #[@test, @values(array(NULL, '127.0.0.1:6100', 1270016100))]
  public function not_equal_to_primitive($value) {
    $this->assertNotEquals($value, new SocketEndpoint('127.0.0.1', 6100));
  }

  #[@test]
  public function v4_address() {
    $this->assertEquals('127.0.0.1:6100', create(new SocketEndpoint('127.0.0.1', 6100))->getAddress());
  }

  #[@test]
  public function v6_address() {
    $this->assertEquals('[fe80::1]:6100', create(new SocketEndpoint('fe80::1', 6100))->getAddress());
  }

  #[@test]
  public function hashcode_returns_address() {
    $this->assertEquals('127.0.0.1:6100', create(new SocketEndpoint('127.0.0.1', 6100))->hashCode());
  }

  #[@test]
  public function value_of_parses_v4_address() {
    $this->assertEquals(new SocketEndpoint('127.0.0.1', 6100), SocketEndpoint::valueOf('127.0.0.1:6100'));
  }

  #[@test]
  public function value_of_parses_v6_address() {
    $this->assertEquals(new SocketEndpoint('fe80::1', 6100), SocketEndpoint::valueOf('[fe80::1]:6100'));
  }

  #[@test, @expect('lang.FormatException')]
  public function value_of_empty_string() {
    SocketEndpoint::valueOf('');
  }

  #[@test, @expect('lang.FormatException')]
  public function value_of_without_colon() {
    SocketEndpoint::valueOf('127.0.0.1');
  }

  #[@test, @expect('lang.FormatException')]
  public function value_of_without_port() {
    SocketEndpoint::valueOf('127.0.0.1:');
  }
}
