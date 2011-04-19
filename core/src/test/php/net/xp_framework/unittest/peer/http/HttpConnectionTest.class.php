<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'peer.http.HttpRequest',
    'peer.http.HttpConstants',
    'net.xp_framework.unittest.peer.http.MockHttpConnection'
  );

  /**
   * TestCase for HTTP connection
   *
   * @see      xp://peer.http.HttpConnection
   * @purpose  Unittest
   */
  class HttpConnectionTest extends TestCase {
  
    /**
     * Test get method
     *
     */
    #[@test]
    public function get() {
      $c= new MockHttpConnection(new URL('http://example.com:80/path/of/file'));
      $c->get(array('var1' => 1, 'var2' => 2));
      $this->assertEquals(
        "GET /path/of/file?var1=1&var2=2 HTTP/1.1\r\nConnection: close\r\nHost: example.com:80\r\n\r\n",
        $c->getLastRequest()->getRequestString()
      );
    }
    
    /**
     * Test head method
     *
     */
    #[@test]
    public function head() {
      $c= new MockHttpConnection(new URL('http://example.com:80/path/of/file'));
      $c->head(array('var1' => 1, 'var2' => 2));
      $this->assertEquals(
        "HEAD /path/of/file?var1=1&var2=2 HTTP/1.1\r\nConnection: close\r\nHost: example.com:80\r\n\r\n",
        $c->getLastRequest()->getRequestString()
      );
    }
  
    /**
     * Test post method
     *
     */
    #[@test]
    public function post() {
      $c= new MockHttpConnection(new URL('http://example.com:80/path/of/file'));
      $c->post(array('var1' => 1, 'var2' => 2));
      $this->assertEquals(
        "POST /path/of/file HTTP/1.1\r\nConnection: close\r\nHost: example.com:80\r\nContent-Length: 13\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\nvar1=1&var2=2",
        $c->getLastRequest()->getRequestString()
      );
    }
  
    /**
     * Test put method
     *
     */
    #[@test]
    public function put() {
      $c= new MockHttpConnection(new URL('http://example.com:80/path/of/file'));
      $c->put(new RequestData('THIS IS A DATA STRING'));
      $this->assertEquals(
        "PUT /path/of/file HTTP/1.1\r\nConnection: close\r\nHost: example.com:80\r\nContent-Length: 21\r\nContent-Type: application/x-www-form-urlencoded\r\n\r\nTHIS IS A DATA STRING",
        $c->getLastRequest()->getRequestString()
      );
    }
  
    /**
     * Test delete method
     *
     */
    #[@test]
    public function delete() {
      $c= new MockHttpConnection(new URL('http://example.com:80/path/of/file'));
      $c->delete(array('var1' => 1, 'var2' => 2));
      $this->assertEquals(
        "DELETE /path/of/file?var1=1&var2=2 HTTP/1.1\r\nConnection: close\r\nHost: example.com:80\r\n\r\n",
        $c->getLastRequest()->getRequestString()
      );
    }
  
    /**
     * Test options method
     *
     */
    #[@test]
    public function options() {
      $c= new MockHttpConnection(new URL('http://example.com:80/path/of/file'));
      $c->options(array('var1' => 1, 'var2' => 2));
      $this->assertEquals(
        "OPTIONS /path/of/file?var1=1&var2=2 HTTP/1.1\r\nConnection: close\r\nHost: example.com:80\r\n\r\n",
        $c->getLastRequest()->getRequestString()
      );
    }
  }
?>
