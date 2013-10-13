<?php namespace net\xp_framework\unittest\webservices\rest;

use unittest\TestCase;
use webservices\rest\RestClient;
use io\streams\MemoryInputStream;


/**
 * TestCase
 *
 * @see   xp://webservices.rest.RestClient
 */
class RestClientSendTest extends TestCase {
  protected static $conn= null;   

  /**
   * Creates connection class which echoes the request
   *
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
   * Test
   *
   */
  #[@test]
  public function withBody() {
    $fixture= new RestClient();
    $fixture->setConnection(self::$conn->newInstance());
    $this->assertEquals(
      "POST / HTTP/1.1\r\n".
      "Connection: close\r\n".
      "Host: test\r\n".
      "Content-Length: 5\r\n".
      "Content-Type: application/x-www-form-urlencoded\r\n".
      "\r\n".
      "Hello",
      $fixture->execute(create(new \webservices\rest\RestRequest('/', \peer\http\HttpConstants::POST))->withBody(new \peer\http\RequestData('Hello')))->content()
    );
  }

  /**
   * Test
   *
   */
  #[@test]
  public function withParameters() {
    $fixture= new RestClient();
    $fixture->setConnection(self::$conn->newInstance());
    $this->assertEquals(
      "POST / HTTP/1.1\r\n".
      "Connection: close\r\n".
      "Host: test\r\n".
      "Content-Length: 9\r\n".
      "Content-Type: application/x-www-form-urlencoded\r\n".
      "\r\n".
      "key=value",
      $fixture->execute(create(new \webservices\rest\RestRequest('/', \peer\http\HttpConstants::POST))->withParameter('key', 'value'))->content()
    );
  }
}
