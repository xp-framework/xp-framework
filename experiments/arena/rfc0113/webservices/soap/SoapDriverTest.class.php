<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'webservices.soap.SoapDriver'
  );

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class SoapDriverTest extends TestCase {

    /**
     * Sets up test case
     *
     */
    public function setUp() {
      if (!extension_loaded('soap')) throw new PrerequisitesNotMetError('ext/soap required.');
      
      // Fetch default driver instance
      $this->driver= SoapDriver::getInstance();
      
      // Register driver without ext/soap capabilities
      $this->xpOnlyDriver= newinstance('webservices.soap.SoapDriver', array(), '{
        public function __construct() {
          parent::__construct();
          
          // Test without ext/soap
          unset($this->drivers[SoapDriver::NATIVE]);
        }
      }');
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function testDriverName() {
      $this->assertEquals(SoapDriver::XP, $this->driver->driverName(SoapDriver::XP));
      $this->assertEquals(SoapDriver::NATIVE, $this->driver->driverName(SoapDriver::NATIVE));
      $this->assertEquals(SoapDriver::NATIVE, $this->driver->driverName(SoapDriver::XP, TRUE));
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function testFromEndpoint() {
      $this->assertClass(
        $this->driver->fromEndpoint('http://localhost', 'uri://foo'),
        'webservices.soap.xp.XPSoapClient'
      );
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function testFromEndpointWithPreferred() {
      $this->assertClass(
        $this->driver->fromEndpoint('http://localhost', 'uri://foo', SoapDriver::NATIVE),
        'webservices.soap.native.NativeSoapClient'
      );

      $this->assertClass(
        $this->driver->fromEndpoint('http://localhost', 'uri://foo', 'NONEXISTANT'),
        'webservices.soap.xp.XPSoapClient'
      );
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function testFromWsdl() {
      $this->assertClass(
        $this->driver->fromWsdl('http://localhost'),
        'webservices.soap.native.NativeSoapClient'
      );
    }
    
    /**
     * Test
     *
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function testFromWsdlWithoutExtSoap() {
      $this->xpOnlyDriver->fromWsdl('http://localhost');
    }
  }
?>
