<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'webservices.rest.server.transport.AbstractHttpRequestAdapter',
    'webservices.json.JsonDecoder'
  );
  
  /**
   * The representation of HTTP request
   *
   * @test xp://net.xp_framework.unittest.rest.server.transport.EmptyHttpRequestAdapterTest
   * @purpose Adapter
   */
  class EmptyHttpRequestAdapter extends AbstractHttpRequestAdapter {
    
    /**
     * Retrieve body
     * 
     * @return var[]
     */
    public function getData() {
      return NULL;
    }
  }
?>
