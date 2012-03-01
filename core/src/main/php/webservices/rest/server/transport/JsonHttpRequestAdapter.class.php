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
   * The JSON representation of HTTP request
   *
   * @test xp://net.xp_framework.unittest.rest.server.transport.JsonHttpRequestAdapterTest
   * @purpose Adapter
   */
  class JsonHttpRequestAdapter extends AbstractHttpRequestAdapter {
    
    /**
     * Retrieve body
     * 
     * @return var[]
     */
    public function getData() {
      if('' === $this->request->getData())
        return null;
      try {
        return create(new JsonDecoder())->decode($this->request->getData());
      } catch(JsonException $e) {
        throw new FormatException('Could not process JSON data. ', $e);
      }
    }
  }
?>
