<?php namespace net\xp_framework\unittest\soap;

use unittest\TestCase;
use webservices\soap\native\NativeSoapTypeMapper;

/**
 * TestCase
 *
 * @see   xp://webservices.soap.native.NativeSoapTypeMapper
 */
class NativeSoapTypeMapperTest extends TestCase {
  protected $fixture= null;

  /**
   * Set up test and create fixture
   *
   */
  public function setUp() {
    if (!\lang\Runtime::getInstance()->extensionAvailable('soap')) {
      throw new \unittest\PrerequisitesNotMetError('PHP Soap extension not available', null, array('ext/soap'));
    }

    $this->fixture= new NativeSoapTypeMapper();
  }

  /**
   * Assertion helper
   *
   * @param   var expected
   * @param   var actual
   * @throws  unittest.AssertionFailedError
   */
  public function assertEqualSoapVar($expected, $actual) {
    if (\xp::stringOf($expected) !== \xp::stringOf($actual)) {
      $this->fail('not equal', $actual, $expected);
    }
  }

  /**
   * Test
   *
   */
  #[@test]
  public function boxInteger() {
    $this->assertEqualSoapVar(new SoapVar(5, XSD_INTEGER), $this->fixture->box(new \lang\types\Integer(5)));
  }

  /**
   * Test
   *
   */
  #[@test]
  public function boxLong() {
    $this->assertEqualSoapVar(new SoapVar('5', XSD_LONG), $this->fixture->box(new Long(5)));
  }

  /**
   * Test
   *
   */
  #[@test]
  public function boxDouble() {
    $this->assertEqualSoapVar(new SoapVar(5.0, XSD_DOUBLE), $this->fixture->box(new \lang\types\Double(5)));
  }

  /**
   * Test
   *
   */
  #[@test]
  public function boxParameter() {
    $this->assertEqualSoapVar(new SoapParam('bar', 'foo'), $this->fixture->box(new Parameter('foo', 'bar')));
  }

  /**
   * Test
   *
   */
  #[@test]
  public function recursiveBoxParameter() {
    $this->assertEqualSoapVar(
      new SoapParam(new SoapVar(5, XSD_INTEGER), 'foo'),
      $this->fixture->box(new Parameter('foo', new \lang\types\Integer(5)))
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function boxBoolean() {
    $this->assertEqualSoapVar(
      new SoapVar(true, XSD_BOOLEAN),
      $this->fixture->box(new \lang\types\Boolean(true))
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function boxBytes() {
    $this->assertEqualSoapVar(
      new SoapVar(base64_encode('ABCDE'), XSD_BASE64BINARY),
      $this->fixture->box(new \lang\types\Bytes('ABCDE'))
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function boxCharacter() {
    $this->assertEqualSoapVar(
      new SoapVar('a', XSD_STRING),
      $this->fixture->box(new \lang\types\Character('a'))
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function boxDate() {
    $this->assertEqualSoapVar(
      new SoapVar('1980-05-28T12:05:00+0200', XSD_DATETIME),
      $this->fixture->box(new \util\Date('1980-05-28T12:05:00+0200'))
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function boxShort() {
    $this->assertEqualSoapVar(
      new SoapVar('127', XSD_SHORT),
      $this->fixture->box(new \lang\types\Short(127))
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function boxString() {
    $this->assertEqualSoapVar(
      new SoapVar('Hello Soap World', XSD_STRING),
      $this->fixture->box(new \lang\types\String('Hello Soap World'))
    );
  }
}
