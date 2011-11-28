<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.unittest.rest.server.transport.AbstractHttpRequestAdapterTest');
  
  /**
   * Test empty HTTP request adapter class
   *
   */
  class EmptyHttpRequestAdapterTest extends AbstractHttpRequestAdapterTest {
    
    /**
     * Return adapter class name
     *
     * @return string
     */
    protected function adapter() {
      return 'webservices.rest.server.transport.EmptyHttpRequestAdapter';
    }
    
    /**
     * Test getData()
     * 
     */
    #[@test]
    public function getData() {
      $this->assertNull($this->fixture->getData());
    }
  }
?>
