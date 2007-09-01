<?php
/* This class is part of the XP framework
 *
 * $Id: XmlRpcClientTest.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace net::xp_framework::unittest::scriptlet::rpc;

  ::uses(
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
  class XmlRpcClientTest extends unittest::TestCase {
  
    /**
     * Test
     *
     */
    #[@test]
    public function simpleRequest() {
      $client= new webservices::xmlrpc::XmlRpcClient(new net::xp_framework::unittest::scriptlet::rpc::dummy::DummyRpcTransport('http://localhost:12345/'));
      $conn= $client->transport->getConnection();
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
