<?php namespace net\xp_framework\unittest\webservices\rest;

use unittest\TestCase;
use webservices\rest\RestClient;
use webservices\rest\RestRequest;
use webservices\rest\RestFormat;
use io\streams\MemoryInputStream;
use peer\http\HttpConstants;
use peer\http\RequestData;

/**
 * TestCase
 *
 * @see   xp://webservices.rest.RestClient
 */
class RestClientSendTest extends TestCase {
  protected static $conn= null;   
  protected $fixture= null;

  /**
   * Creates connection class which echoes the request
   */
  #[@beforeClass]
  public static function requestEchoingConnectionClass() {
    self::$conn= \lang\ClassLoader::defineClass('RestClientSendTest_Connection', 'peer.http.HttpConnection', array(), '{
      public function __construct() {
        parent::__construct("http://test");
      }
      
      public function send(HttpRequest $request) {
        $str= $request->getRequestString();
        return new HttpResponse(new MemoryInputStream(sprintf(
          "HTTP/1.0 200 OK\r\nContent-Type: text/plain\r\nContent-Length: %d\r\n\r\n%s",
          strlen($str),
          $str
        )));
      }
    }');
  }

  /**
   * Creates fixture.
   */
  public function setUp() {
    $this->fixture= new RestClient();
    $this->fixture->setConnection(self::$conn->newInstance());
  }

  #[@test]
  public function with_body() {
    $this->assertEquals(
      "POST / HTTP/1.1\r\n".
      "Connection: close\r\n".
      "Host: test\r\n".
      "Content-Length: 5\r\n".
      "Content-Type: application/x-www-form-urlencoded\r\n".
      "\r\n".
      "Hello",
      $this->fixture->execute(create(new RestRequest('/', HttpConstants::POST))->withBody(new RequestData('Hello')))->content()
    );
  }

  #[@test]
  public function with_json_payload() {
    $this->assertEquals(
      "POST / HTTP/1.1\r\n".
      "Connection: close\r\n".
      "Host: test\r\n".
      "Content-Type: application/json; charset=utf-8\r\n".
      "Content-Length: 6\r\n".
      "\r\n".
      "\"Test\"",
      $this->fixture->execute(create(new RestRequest('/', HttpConstants::POST))->withPayload('Test', RestFormat::$JSON))->content()
    );
  }

  #[@test]
  public function with_xml_payload() {
    $this->assertEquals(
      "POST / HTTP/1.1\r\n".
      "Connection: close\r\n".
      "Host: test\r\n".
      "Content-Type: text/xml; charset=utf-8\r\n".
      "Content-Length: 56\r\n".
      "\r\n".
      "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
      "<root>Test</root>",
      $this->fixture->execute(create(new RestRequest('/', HttpConstants::POST))->withPayload('Test', RestFormat::$XML))->content()
    );
  }

  #[@test]
  public function with_parameters() {
    $this->assertEquals(
      "POST / HTTP/1.1\r\n".
      "Connection: close\r\n".
      "Host: test\r\n".
      "Content-Length: 9\r\n".
      "Content-Type: application/x-www-form-urlencoded\r\n".
      "\r\n".
      "key=value",
      $this->fixture->execute(create(new RestRequest('/', HttpConstants::POST))->withParameter('key', 'value'))->content()
    );
  }
}
