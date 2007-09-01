<?php
/* This class is part of the XP framework
 *
 * $Id: HttpTest.class.php 8975 2006-12-27 18:06:40Z friebe $ 
 */

  namespace net::xp_framework::unittest::peer;
 
  ::uses(
    'peer.http.HttpConnection',
    'unittest.TestCase'
  );

  /**
   * Test HTTP API
   *
   * Needs a counter part, a PHP script running under a webserver.
   * This script shall contain the following source code:
   * <code>
   *   $str= serialize(array(
   *     'headers'   => getallheaders(),
   *     'method'    => getenv('REQUEST_METHOD'),
   *     'uri'       => getenv('REQUEST_URI'),
   *     'request'   => $_REQUEST
   *   ));
   *   header('Content-type: application/x-php-serialized');
   *   header('Content-length: '.strlen($str));
   * 
   *   echo $str;
   * </code>
   *
   * @purpose  Unit Test
   */
  class HttpTest extends unittest::TestCase {
    public
      $conn = NULL,
      $uri  = '';
      
    /**
     * Constructor
     *
     * @param   string name
     * @param   string uri
     */
    public function __construct($name, $uri) {
      $this->uri= $uri;
      parent::__construct($name);
    }
      
    /**
     * Setup function
     *
     * @throws  rdbms.DriverNotSupportedException
     */
    public function setUp() {
      $this->conn= new peer::http::HttpConnection($this->uri);
    }
    
    /**
     * Private helper method
     *
     * @param   string method
     * @param   bool expectingData
     * @return  array data
     */
    protected function _testRequest($method, $expectingData) {
      try {
        $response= $this->conn->request($method, array(
          'a'   => 'b',
          'b'   => 'c'
        ));
      } catch (::Exception $e) {
        throw($e);
      }
      if (!$this->assertSubclass($response, 'peer.http.HttpResponse')) return;

      // Check headers
      $length= $response->getHeader('Content-length');
      $this->assertNotEmpty($length, 'contentlength.missing');
      $ctype= $response->getHeader('Content-type');
      $this->assertNotEmpty($length, 'contenttype.missing');
      
      $data= NULL;
      if ($expectingData) {
        $buf= $response->readData($length, TRUE);
        $this->assertEquals(strlen($buf), (int)$length, 'readdata');
        $data= unserialize($buf);
        $this->assertArray($data, 'data.corrupt');

        // Check return
        $this->assertArray($data['headers'], 'requestheaders.missing');
        $this->assertEquals($data['method'], $method, 'requestmethod');
        $this->assertArray($data['request'], 'querymissing');
        $this->assertEquals($data['request']['a'], 'b', 'query.datamissing');
        $this->assertEquals($data['request']['b'], 'c', 'query.datamissing');
      }
      
      return array($response->getHeaders(), $data);
    }
    
    /**
     * Test get method
     *
     */
    #[@test]
    public function testGet() {
      return $this->_testRequest(HTTP_GET, TRUE);
    }

    /**
     * Test post method
     *
     */
    #[@test]
    public function testPost() {
      return $this->_testRequest(HTTP_POST, TRUE);
    }

    /**
     * Test head method
     *
     */
    #[@test]
    public function testHead() {
      return $this->_testRequest(HTTP_HEAD, FALSE);
    }
  }
?>
