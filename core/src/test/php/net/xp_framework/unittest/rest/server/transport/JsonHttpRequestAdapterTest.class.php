<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.unittest.rest.server.transport.AbstractHttpRequestAdapterTest');
  
  /**
   * Test JSON HTTP request adapter class
   *
   */
  class JsonHttpRequestAdapterTest extends AbstractHttpRequestAdapterTest {
  
    /**
     * Return adapter class name
     *
     * @return string
     */
    protected function adapter() {
      return 'webservices.rest.server.transport.JsonHttpRequestAdapter';
    }
    
    /**
     * Test getData()
     * 
     */
    #[@test]
    public function getData() {
      $decoder= new JsonDecoder();
      $data= array('some' => new String('thing'));
      
      $this->request->setData($decoder->encode($data));
      $this->assertEquals($data, $this->fixture->getData());
    }
  }
?>
