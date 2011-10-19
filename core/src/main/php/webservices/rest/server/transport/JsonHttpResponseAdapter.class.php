<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'webservices.rest.server.transport.AbstractHttpResponseAdapter',
    'webservices.json.JsonDecoder'
  );
  
  /**
   * The JSON representation of HTTP response
   *
   * @test xp://net.xp_framework.unittest.rest.server.transport.JsonHttpResponseAdapterTest
   * @purpose Adapter
   */
  class JsonHttpResponseAdapter extends AbstractHttpResponseAdapter {
    
    /**
     * Set body
     * 
     * @param var[] data The data
     */
    public function setData($data) {
      $this->response->setHeader('Content-Type', 'application/json');
      $this->response->write(create(new JsonDecoder())->encode($data));
    }
  }
?>
