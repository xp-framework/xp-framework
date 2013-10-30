<?php namespace net\xp_framework\unittest\peer\http;

use unittest\TestCase;
use peer\http\RequestData;
use peer\http\FormRequestData;
use peer\http\FileData;
use peer\http\FormData;
use peer\http\HttpRequest;
use peer\http\HttpConstants;

/**
 * TestCase for HTTP request construction
 *
 * @see   xp://peer.http.HttpRequest
 * @see   https://github.com/xp-framework/xp-framework/issues/335
 */
class HttpRequestTest extends TestCase {

  #[@test]
  public function getSimpleUrl() {
    $r= new HttpRequest(new \peer\URL('http://example.com'));
    $r->setMethod(HttpConstants::GET);
    $this->assertEquals(
      "GET / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
      $r->getRequestString()
    );
  }

  #[@test]
  public function portIncluded() {
    $r= new HttpRequest(new \peer\URL('http://example.com:8080'));
    $r->setMethod(HttpConstants::GET);
    $this->assertEquals(
      "GET / HTTP/1.1\r\nConnection: close\r\nHost: example.com:8080\r\n\r\n",
      $r->getRequestString()
    );
  }

  #[@test]
  public function getUrlWithPath() {
    $r= new HttpRequest(new \peer\URL('http://example.com/path/to/images/index.html'));
    $r->setMethod(HttpConstants::GET);
    $this->assertEquals(
      "GET /path/to/images/index.html HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
      $r->getRequestString()
    );
  }

  #[@test]
  public function getSupportsBasicAuth() {
    $r= new HttpRequest(new \peer\URL('http://user:pass@example.com/'));
    $r->setMethod(HttpConstants::GET);
    $this->assertEquals(
      "GET / HTTP/1.1\r\nConnection: close\r\nAuthorization: Basic dXNlcjpwYXNz\r\nHost: example.com\r\n\r\n",
      $r->getRequestString()
    );
  }

  #[@test]
  public function getUrlWithFileOnly() {
    $r= new HttpRequest(new \peer\URL('http://example.com/index.html'));
    $r->setMethod(HttpConstants::GET);
    $this->assertEquals(
      "GET /index.html HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
      $r->getRequestString()
    );
  }

  #[@test]
  public function getUrlWithUrlParams() {
    $r= new HttpRequest(new \peer\URL('http://example.com/?a=b'));
    $r->setMethod(HttpConstants::GET);
    $this->assertEquals(
      "GET /?a=b HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
      $r->getRequestString()
    );
  }

  #[@test]
  public function getUrlWithArrayParams() {
    $r= new HttpRequest(new \peer\URL('http://example.com/'));
    $r->setMethod(HttpConstants::GET);
    $r->setParameters(array('a' => 'b'));
    $this->assertEquals(
      "GET /?a=b HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
      $r->getRequestString()
    );
  }

  #[@test]
  public function getUrlWithStringParams() {
    $r= new HttpRequest(new \peer\URL('http://example.com/'));
    $r->setMethod(HttpConstants::GET);
    $r->setParameters('a=b');
    $this->assertEquals(
      "GET /?a=b HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
      $r->getRequestString()
    );
  }

  #[@test]
  public function getUrlWithStringContainingArrayParams() {
    $r= new HttpRequest(new \peer\URL('http://example.com/'));
    $r->setParameters('data[color]=green&data[size]=S');
    $r->setMethod(HttpConstants::GET);
    $this->assertEquals(
      "GET /?data[color]=green&data[size]=S HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
      $r->getRequestString()
    );
  }

  #[@test]
  public function getUrlWithRequestDataParams() {
    $r= new HttpRequest(new \peer\URL('http://example.com/'));
    $r->setMethod(HttpConstants::GET);
    $r->setParameters(new RequestData('a=b&c=d'));
    $this->assertEquals(
      "GET /?a=b&c=d HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
      $r->getRequestString()
    );
  }

  #[@test]
  public function postUrlWithFormRequestDataParams() {
    $r= new HttpRequest(new \peer\URL('http://example.com/'));
    $r->setMethod(HttpConstants::POST);
    $r->setParameters(new FormRequestData(array(
      new FormData('key', 'value'),
      new FormData('xml', '<foo/>', 'text/xml')
    )));

    // Fetch randomly generated boundary
    $this->assertTrue($r->parameters instanceof FormRequestData);
    $boundary= $r->parameters->getBoundary();

    $this->assertEquals(
      "POST / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n".
      "Content-Type: multipart/form-data; boundary=".$boundary."\r\nContent-Length: 265\r\n\r\n".
      "--".$boundary."\r\nContent-Disposition: form-data; name=\"key\"\r\n".
      "\r\nvalue\r\n".
      "--".$boundary."\r\n".
      "Content-Disposition: form-data; name=\"xml\"\r\n".
      "Content-Type: text/xml\r\n".
      "\r\n<foo/>\r\n".
      "--".$boundary."--\r\n",
      $r->getRequestString()
    );
  }

  #[@test]
  public function postUrlWithFileDataParams() {
    $r= new HttpRequest(new \peer\URL('http://example.com/'));
    $r->setMethod(HttpConstants::POST);
    $r->setParameters(new FormRequestData(array(
      new FileData('file', 'image.jpeg', new \io\streams\MemoryInputStream('JFIF...'), 'image/jpeg')
      new FileData('file', 'attach.txt', new \io\streams\MemoryInputStream('Test'), 'text/plain')
    )));

    // Fetch randomly generated boundary
    $this->assertTrue($r->parameters instanceof FormRequestData);
    $boundary= $r->parameters->getBoundary();

    $this->assertEquals(
      "POST / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n".
      "Content-Type: multipart/form-data; boundary=".$boundary."\r\nContent-Length: 265\r\n\r\n".
      "--".$boundary."\r\nContent-Disposition: form-data; name=\"key\"\r\n".
      "\r\nvalue\r\n".
      "--".$boundary."\r\n".
      "Content-Disposition: form-data; name=\"xml\"\r\n".
      "Content-Type: text/xml\r\n".
      "\r\n<foo/>\r\n".
      "--".$boundary."--\r\n",
      $r->getRequestString()
    );
  }

  #[@test]
  public function getUrlWithArrayAndUrlParams() {
    $r= new HttpRequest(new \peer\URL('http://example.com/?a=b'));
    $r->setMethod(HttpConstants::GET);
    $r->setParameters(array('a' => 'b'));
    $this->assertEquals(
      "GET /?a=b HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
      $r->getRequestString()
    );
  }

  #[@test]
  public function getUrlWithArrayFromUrlParams() {
    $r= new HttpRequest(new \peer\URL('http://example.com/?data[color]=green&data[size]=S'));
    $r->setMethod(HttpConstants::GET);
    $this->assertEquals(
      "GET /?data[color]=green&data[size]=S HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
      $r->getRequestString()
    );
  }


  #[@test]
  public function getUrlWithArrayParameter() {
    $r= new HttpRequest(new \peer\URL('http://example.com/'));
    $r->setMethod(HttpConstants::GET);
    $r->setParameters(array('params' => array('target' => 'home', 'ssl' => 'true')));
    $this->assertEquals(
      "GET /?params[target]=home&params[ssl]=true HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
      $r->getRequestString()
    );
  }

  #[@test]
  public function getUrlWithStringAndUrlParams() {
    $r= new HttpRequest(new \peer\URL('http://example.com/?a=b'));
    $r->setMethod(HttpConstants::GET);
    $r->setParameters('a=b');
    $this->assertEquals(
      "GET /?a=b HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
      $r->getRequestString()
    );
  }

  #[@test]
  public function post() {
    $r= new HttpRequest(new \peer\URL('http://example.com/'));
    $r->setMethod(HttpConstants::POST);
    $r->setParameters('a=b&c=d');
    $this->assertEquals(
      "POST / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n".
      "Content-Length: 7\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\n".
      "a=b&c=d",
      $r->getRequestString()
    );
  }

  #[@test]
  public function postUrlWithStringContainingArrayParams() {
    $r= new HttpRequest(new \peer\URL('http://example.com/'));
    $r->setParameters('data[color]=green&data[size]=S');
    $r->setMethod(HttpConstants::POST);
    $this->assertEquals(
      "POST / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n".
      "Content-Length: 30\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\n".
      "data[color]=green&data[size]=S",
      $r->getRequestString()
    );
  }

  #[@test]
  public function postUrlWithArrayParams() {
    $r= new HttpRequest(new \peer\URL('http://example.com/'));
    $r->setParameters(array('data' => array('color' => 'green', 'size' => 'S')));
    $r->setMethod(HttpConstants::POST);
    $this->assertEquals(
      "POST / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n".
      "Content-Length: 30\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\n".
      "data[color]=green&data[size]=S",
      $r->getRequestString()
    );
  }

  #[@test]
  public function put() {
    $r= new HttpRequest(new \peer\URL('http://example.com/'));
    $r->setMethod(HttpConstants::PUT);
    $r->setParameters('a=b&c=d');
    $this->assertEquals(
      "PUT / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n".
      "Content-Length: 7\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\n".
      "a=b&c=d",
      $r->getRequestString()
    );
  }

  #[@test]
  public function trace() {
    $r= new HttpRequest(new \peer\URL('http://example.com/'));
    $r->setMethod(HttpConstants::TRACE);
    $r->setParameters('a=b&c=d');
    $this->assertEquals(
      "TRACE / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n".
      "Content-Length: 7\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\n".
      "a=b&c=d",
      $r->getRequestString()
    );
  }

  #[@test]
  public function head() {
    $r= new HttpRequest(new \peer\URL('http://example.com/'));
    $r->setMethod(HttpConstants::HEAD);
    $r->setParameters('a=b&c=d');
    $this->assertEquals(
      "HEAD /?a=b&c=d HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
      $r->getRequestString()
    );
  }

  #[@test]
  public function delete() {
    $r= new HttpRequest(new \peer\URL('http://example.com/'));
    $r->setMethod(HttpConstants::DELETE);
    $r->setParameters('a=b&c=d');
    $this->assertEquals(
      "DELETE /?a=b&c=d HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
      $r->getRequestString()
    );
  }

  #[@test]
  public function options() {
    $r= new HttpRequest(new \peer\URL('http://example.com/'));
    $r->setMethod(HttpConstants::OPTIONS);
    $r->setParameters('a=b&c=d');
    $this->assertEquals(
      "OPTIONS /?a=b&c=d HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
      $r->getRequestString()
    );
  }

  #[@test]
  public function customHeader() {
    $r= new HttpRequest(new \peer\URL('http://example.com/'));
    $r->setHeader('X-Binford', 6100);
    $this->assertEquals(
      "GET / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\nX-Binford: 6100\r\n\r\n",
      $r->getRequestString()
    );
  }

  #[@test]
  public function customHeaderObject() {
    $r= new HttpRequest(new \peer\URL('http://example.com/'));
    $r->setHeader('X-Binford', new \peer\Header('X-Binford', 6100));
    $this->assertEquals(
      "GET / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\nX-Binford: 6100\r\n\r\n",
      $r->getRequestString()
    );
  }

  #[@test]
  public function customHeaderList() {
    $r= new HttpRequest(new \peer\URL('http://example.com/'));
    $r->setHeader('X-Binford', array(6100, 'More Power'));
    $this->assertEquals(
      "GET / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\nX-Binford: 6100\r\nX-Binford: More Power\r\n\r\n",
      $r->getRequestString()
    );
  }

  #[@test]
  public function customHeaders() {
    $r= new HttpRequest(new \peer\URL('http://example.com/'));
    $r->addHeaders(array('X-Binford' => 6100));
    $this->assertEquals(
      "GET / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\nX-Binford: 6100\r\n\r\n",
      $r->getRequestString()
    );
  }

  #[@test]
  public function customHeadersObject() {
    $r= new HttpRequest(new \peer\URL('http://example.com/'));
    $r->addHeaders(array('X-Binford' => new \peer\Header('X-Binford', 6100)));
    $this->assertEquals(
      "GET / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\nX-Binford: 6100\r\n\r\n",
      $r->getRequestString()
    );
  }

  #[@test]
  public function customHeadersObjectList() {
    $r= new HttpRequest(new \peer\URL('http://example.com/'));
    $r->addHeaders(array(new \peer\Header('X-Binford', 6100)));
    $this->assertEquals(
      "GET / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\nX-Binford: 6100\r\n\r\n",
      $r->getRequestString()
    );
  }

  #[@test]
  public function customHeadersList() {
    $r= new HttpRequest(new \peer\URL('http://example.com/'));
    $r->addHeaders(array('X-Binford' => array(6100, 'Even more power')));
    $this->assertEquals(
      "GET / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\nX-Binford: 6100\r\nX-Binford: Even more power\r\n\r\n",
      $r->getRequestString()
    );
  }

  #[@test]
  public function duplicateHeader() {
    $r= new HttpRequest(new \peer\URL('http://example.com/'));
    $r->setHeader('X-Binford', 6100);
    $r->setHeader('X-Binford', 61000);
    $this->assertEquals(
      "GET / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\nX-Binford: 61000\r\n\r\n",
      $r->getRequestString()
    );
  }

  #[@test]
  public function headerString() {
    $r= new HttpRequest(new \peer\URL('http://example.com/'));
    $r->setMethod(HttpConstants::GET);
    $r->setParameters('a=b');
    $this->assertEquals(
      "GET /?a=b HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\n\r\n",
      $r->getHeaderString()
    );
  }

  #[@test]
  public function headerStringDoesNotIncludeContent() {
    $r= new HttpRequest(new \peer\URL('http://example.com/'));
    $r->setMethod(HttpConstants::POST);
    $r->setParameters('a=b');
    $this->assertEquals(
      "POST / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\nContent-Length: 3\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\n",
      $r->getHeaderString()
    );
  }

  #[@test]
  public function emptyPostBody() {
    $r= new HttpRequest(new \peer\URL('http://example.com/'));
    $r->setMethod(HttpConstants::POST);
    $r->setParameters('');
    $this->assertEquals(
      "POST / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\nContent-Length: 0\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\n",
      $r->getHeaderString()
    );
  }

  #[@test]
  public function oneByteBody() {
    $r= new HttpRequest(new \peer\URL('http://example.com/'));
    $r->setMethod(HttpConstants::POST);
    $r->setParameters(new RequestData('1'));
    $this->assertEquals(
      "POST / HTTP/1.1\r\nConnection: close\r\nHost: example.com\r\nContent-Length: 1\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\n",
      $r->getHeaderString()
    );
  }
}
