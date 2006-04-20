<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.unittest.TestCase',
    'xml.xmlrpc.XmlRpcClient',
    'net.xp_framework.unittest.scriptlet.rpc.dummy.DummyRpcTransport'
  );

  /**
   * Testcase for XmlRpcClient
   *
   * @see      xp://xml.xmlrpc.XmlRpcClient
   * @purpose  TestCase
   */
  class XmlRpcClientTest extends TestCase {
  
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
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
      
      $this->assertEquals(array('foobar'), $client->invoke('Foo'));
    }
  }
?>
