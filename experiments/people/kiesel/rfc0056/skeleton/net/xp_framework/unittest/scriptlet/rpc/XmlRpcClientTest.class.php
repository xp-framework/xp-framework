<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'webservices.xmlrpc.XmlRpcClient',
    'net.xp_framework.unittest.scriptlet.rpc.dummy.DummyRpcTransport'
  );

  /**
   * Testcase for XmlRpcClient
   *
   * @see      xp://webservices.xmlrpc.XmlRpcClient
   * @purpose  TestCase
   */
  class XmlRpcClientTest extends TestCase {
  
    /**
     * Test
     *
     * @access  public
     */
    #[@test]
    function simpleRequest() {
      $client= &new XmlRpcClient(new DummyRpcTransport('http://localhost:12345/'));
      $conn= &$client->transport->getConnection();
      $conn->request->setResponse('HTTP/1.1 200 Ok
Content-type: text/xml
X-Server: PHP

<?xml version="1.0" encoding="iso-8859-1"?>
<methodResponse>
  <params>
    <param>
      <value>
        <string>foobar</string>
      </value>
    </param>
  </params>
</methodResponse>');
      
      $this->assertEquals('foobar', $client->invoke('Foo'));
    }
  }
?>
