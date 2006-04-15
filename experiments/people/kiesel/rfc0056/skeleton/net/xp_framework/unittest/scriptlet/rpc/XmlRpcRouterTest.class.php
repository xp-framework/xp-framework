<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.unittest.TestCase',
    'net.xp_framework.unittest.scriptlet.rpc.XmlRpcRouterMock'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class XmlRpcRouterTest extends TestCase {
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setUp() {
      $this->router= &new XmlRpcRouterMock(new ClassLoader('net.xp_framework.unittest.scriptlet.rpc.impl'));
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    #[@test]
    function testSetup() {
      $this->router->setMockHeaders(array());
      $this->router->setMockParams(array());
      $this->router->setMockMethod(HTTP_POST);
      $this->router->setMockData('<?xml version="1.0" encoding="iso-8859-1"?>
<methodCall>
  <methodName>DummyRpcImplementation.getImplementationName</methodName>
  <params/>
</methodCall>
'     );
      
      $this->router->init();
      $response= &$this->router->process();
      
      $this->assertEquals(200, $response->statusCode);
    }
    
  }
?>
