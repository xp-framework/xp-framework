<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.http.HttpConnection',
    'util.profiling.unittest.TestCase'
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
  class HttpTest extends TestCase {
    public
      $conn = NULL,
      $uri  = '';
      
    /**
     * Constructor
     *
     * @access  publuc
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
     * @access  public
     * @throws  rdbms.DriverNotSupportedException
     */
    public function setUp() {
      $this->conn= new HttpConnection($this->uri);
    }
    
    /**
     * Private helper method
     *
     * @access  private
     * @param   string method
     * @param   bool expectingData
     * @return  array data
     */
    private function _test($method, $expectingData) {
      try {
        $response= $this->conn->request($method, array(
          'a'   => 'b',
          'b'   => 'c'
        ));
      } catch (XPException $e) {
        return self::fail($e->getClassName(), $e->getStackTrace(), $method);
      }
      if (!self::assertSubclass($response, 'peer.http.HttpResponse')) return;

      // Check headers
      $length= $response->getHeader('Content-length');
      self::assertNotEmpty($length, 'contentlength.missing');
      $ctype= $response->getHeader('Content-type');
      self::assertNotEmpty($length, 'contenttype.missing');
      
      $data= NULL;
      if ($expectingData) {
        $buf= $response->readData($length, TRUE);
        self::assertEquals(strlen($buf), (int)$length, 'readdata');
        $data= unserialize($buf);
        self::assertArray($data, 'data.corrupt');

        // Check return
        self::assertArray($data['headers'], 'requestheaders.missing');
        self::assertEquals($data['method'], $method, 'requestmethod');
        self::assertArray($data['request'], 'querymissing');
        self::assertEquals($data['request']['a'], 'b', 'query.datamissing');
        self::assertEquals($data['request']['b'], 'c', 'query.datamissing');
      }
      
      return array($response->getHeaders(), $data);
    }
    
    /**
     * Test get method
     *
     * @access  public
     */
    public function testGet() {
      return self::_test(HTTP_GET, TRUE);
    }

    /**
     * Test post method
     *
     * @access  public
     */
    public function testPost() {
      return self::_test(HTTP_POST, TRUE);
    }

    /**
     * Test head method
     *
     * @access  public
     */
    public function testHead() {
      return self::_test(HTTP_HEAD, FALSE);
    }
  }
?>
