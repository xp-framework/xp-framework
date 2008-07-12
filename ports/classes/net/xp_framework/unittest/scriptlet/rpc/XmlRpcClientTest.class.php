<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'io.streams.MemoryInputStream',
    'peer.http.HttpResponse',
    'webservices.xmlrpc.XmlRpcResponseMessage',
    'webservices.xmlrpc.transport.XmlRpcHttpTransport'
  );

  /**
   * Testcase for XmlRpcClient
   *
   * @see      xp://webservices.xmlrpc.XmlRpcHttpTransport
   * @purpose  TestCase
   */
  class XmlRpcClientTest extends TestCase {

    /**
     * Get a response with the specified headers and body
     *
     * @param   string[] headers
     * @param   string body default ''
     * @return  peer.http.HttpResponse
     */
    protected function newResponse(array $headers, $body= '') {
      return create(new XmlRpcHttpTransport('http://localhost'))->retrieve(
        new HttpResponse(new MemoryInputStream(implode("\r\n", $headers)."\r\n\r\n".trim($body)))
      );
    }
  
    /**
     * Test
     *
     */
    #[@test]
    public function stringAnswer() {
      $response= $this->newResponse(array('HTTP/1.0 200 OK', 'Content-Type: text/xml'), '
        <?xml version="1.0" encoding="iso-8859-1"?>
        <methodResponse>
          <params>
            <param>
              <value>
                <string>foobar</string>
              </value>
            </param>
          </params>
        </methodResponse>'
      );
      
      $this->assertEquals('foobar', $response->getData());
    }

    /**
     * Test an empty response
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function emptyResponse() {
      $response= $this->newResponse(array('HTTP/1.0 200 OK', 'Content-Type: text/xml'), '
        <?xml version="1.0" encoding="iso-8859-1"?>
        <methodResponse/>
      ');
      
      $this->assertEquals('foobar', $response->getData());
    }

    /**
     * Test a 401 response
     *
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function unauthorized() {
      $this->newResponse(array('HTTP/1.0 401 Unauthorized', 'Content-Type: text/html'), '
        <html><head>401</head><body><h1>401 Unauthorized</h1></body></html>
      ');
    }

    /**
     * Test a 302 response
     *
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function redirect() {
      $this->newResponse(array('HTTP/1.0 302 Moved Temporarily', 'Location: http://example.com'));
    }

    /**
     * Test a fault
     *
     */
    #[@test]
    public function fault() {
      try {
        $this->newResponse(array('HTTP/1.0 500 Internal Server error', 'Content-Type: text/xml'), '
          <?xml version="1.0" encoding="iso-8859-1"?>
          <methodResponse>
            <fault>
              <value>
                <struct>
                  <member>
                    <name>faultCode</name>
                    <value><int>23</int></value>
                  </member>
                  <member>
                    <name>faultString</name>
                    <value><string>Zip code does not match state</string></value>
                  </member>
                </struct>
              </value>
            </fault>
          </methodResponse>
        ');
        $this->fail('Expected exception not caught', 'webservices.xmlrpc.XmlRpcFaultException', NULL);
      } catch (XmlRpcFaultException $e) {
        $this->assertEquals(23, $e->getFault()->getFaultCode());
        $this->assertEquals('Zip code does not match state', $e->getFault()->getFaultString());
      }
    }
  }
?>
