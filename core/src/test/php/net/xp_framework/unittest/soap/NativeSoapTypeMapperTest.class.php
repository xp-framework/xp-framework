<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'webservices.soap.native.NativeSoapTypeMapper'
  );

  /**
   * TestCase
   *
   * @see   xp://webservices.soap.native.NativeSoapTypeMapper
   */
  class NativeSoapTypeMapperTest extends TestCase {
    protected $fixture= NULL;

    /**
     * Set up test and create fixture
     *
     */
    public function setUp() {
      if (!Runtime::getInstance()->extensionAvailable('soap')) {
        throw new PrerequisitesNotMetError('PHP Soap extension not available', NULL, array('ext/soap'));
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
      if (xp::stringOf($expected) !== xp::stringOf($actual)) {
        $this->fail('not equal', $actual, $expected);
      }
    }

    /**
     * Test
     *
     */
    #[@test]
    public function boxInteger() {
      $this->assertEqualSoapVar(new SoapVar(5, XSD_INTEGER), $this->fixture->box(new Integer(5)));
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
      $this->assertEqualSoapVar(new SoapVar(5.0, XSD_DOUBLE), $this->fixture->box(new Double(5)));
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
        $this->fixture->box(new Parameter('foo', new Integer(5)))
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function boxBoolean() {
      $this->assertEqualSoapVar(
        new SoapVar(TRUE, XSD_BOOLEAN),
        $this->fixture->box(new Boolean(TRUE))
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
        $this->fixture->box(new Bytes('ABCDE'))
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
        $this->fixture->box(new Character('a'))
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
        $this->fixture->box(new Date('1980-05-28T12:05:00+0200'))
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
        $this->fixture->box(new Short(127))
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
        $this->fixture->box(new String('Hello Soap World'))
      );
    }
  }
?>
