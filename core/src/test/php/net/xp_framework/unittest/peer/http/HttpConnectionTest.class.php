<?php namespace net\xp_framework\unittest\peer\http;

use unittest\TestCase;
use peer\http\HttpRequest;
use peer\http\HttpConstants;
use peer\http\RequestData;
use util\log\LogCategory;
use util\log\BufferedAppender;
use util\log\layout\PatternLayout;

/**
 * TestCase for HTTP connection
 *
 * @see      xp://peer.http.HttpConnection
 */
class HttpConnectionTest extends TestCase {
  protected $fixture= null;

  /**
   * Creates fixture member.
   */
  public function setUp() {
    $this->fixture= new MockHttpConnection(new \peer\URL('http://example.com:80/path/of/file'));
  }

  #[@test]
  public function get() {
    $this->fixture->get(array('var1' => 1, 'var2' => 2));
    $this->assertEquals(
      "GET /path/of/file?var1=1&var2=2 HTTP/1.1\r\nConnection: close\r\nHost: example.com:80\r\n\r\n",
      $this->fixture->getLastRequest()->getRequestString()
    );
  }
  
  #[@test]
  public function head() {
    $this->fixture->head(array('var1' => 1, 'var2' => 2));
    $this->assertEquals(
      "HEAD /path/of/file?var1=1&var2=2 HTTP/1.1\r\nConnection: close\r\nHost: example.com:80\r\n\r\n",
      $this->fixture->getLastRequest()->getRequestString()
    );
  }

  #[@test]
  public function post() {
    $this->fixture->post(array('var1' => 1, 'var2' => 2));
    $this->assertEquals(
      "POST /path/of/file HTTP/1.1\r\nConnection: close\r\nHost: example.com:80\r\nContent-Length: 13\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\nvar1=1&var2=2",
      $this->fixture->getLastRequest()->getRequestString()
    );
  }

  #[@test]
  public function put() {
    $this->fixture->put(new RequestData('THIS IS A DATA STRING'));
    $this->assertEquals(
      "PUT /path/of/file HTTP/1.1\r\nConnection: close\r\nHost: example.com:80\r\nContent-Length: 21\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\nTHIS IS A DATA STRING",
      $this->fixture->getLastRequest()->getRequestString()
    );
  }

  #[@test]
  public function patch() {
    $this->fixture->patch(new RequestData('THIS IS A DATA STRING'));
    $this->assertEquals(
      "PATCH /path/of/file HTTP/1.1\r\nConnection: close\r\nHost: example.com:80\r\nContent-Length: 21\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\nTHIS IS A DATA STRING",
      $this->fixture->getLastRequest()->getRequestString()
    );
  }

  #[@test]
  public function delete() {
    $this->fixture->delete(array('var1' => 1, 'var2' => 2));
    $this->assertEquals(
      "DELETE /path/of/file?var1=1&var2=2 HTTP/1.1\r\nConnection: close\r\nHost: example.com:80\r\n\r\n",
      $this->fixture->getLastRequest()->getRequestString()
    );
  }

  #[@test]
  public function options() {
    $this->fixture->options(array('var1' => 1, 'var2' => 2));
    $this->assertEquals(
      "OPTIONS /path/of/file?var1=1&var2=2 HTTP/1.1\r\nConnection: close\r\nHost: example.com:80\r\n\r\n",
      $this->fixture->getLastRequest()->getRequestString()
    );
  }

  #[@test]
  public function create_and_send() {
    with ($request= $this->fixture->create(new HttpRequest())); {
      $request->setMethod('PROPPATCH');   // Webdav
      $request->setTarget('/');
      $this->fixture->send($request);
    }
    $this->assertEquals(
      "PROPPATCH / HTTP/1.1\r\nConnection: close\r\nHost: example.com:80\r\nContent-Length: 0\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\n",
      $this->fixture->getLastRequest()->getRequestString()
    );
  }

  #[@test]
  public function is_traceable() {
    $this->assertInstanceOf('util.log.Traceable', $this->fixture);
  }

  #[@test]
  public function tracing() {
    $appender= create(new BufferedAppender())->withLayout(new PatternLayout('%m'));
    $this->fixture->setTrace(create(new LogCategory('trace'))->withAppender($appender));
    $this->fixture->get();
    $this->assertEquals(
      ">>> GET /path/of/file HTTP/1.1\r\nConnection: close\r\nHost: example.com:80\r\n\r\n".
      "<<< HTTP/1.0 200 Testing OK\r\n\r\n",
      $appender->getBuffer()
    );
  }

  #[@test]
  public function changing_request_target_does_not_modify_connection_url() {
    $url= $this->fixture->getUrl();
    $this->fixture->create(new HttpRequest())->setTarget('/foo');
    $this->assertNotEquals('/foo', $url->getPath());
  }
}
