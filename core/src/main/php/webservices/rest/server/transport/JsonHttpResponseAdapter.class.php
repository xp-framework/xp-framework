<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'webservices.rest.server.transport.AbstractHttpResponseAdapter',
    'webservices.rest.server.RestDataCaster',
    'webservices.json.JsonDecoder'
  );
  
  /**
   * The JSON representation of HTTP response
   *
   * @test    xp://net.xp_framework.unittest.rest.server.transport.JsonHttpResponseAdapterTest
   */
  class JsonHttpResponseAdapter extends AbstractHttpResponseAdapter {
    private 
      $dataCaster= NULL;

    /**
     * Constructor
     * 
     * @param   scriptlet.HttpResponse response The response
     * @param   webservices.rest.server.RestDataCaster dataCaster The data caster, optionally.
     */
    public function __construct($response, RestDataCaster $dataCaster= NULL) {
      parent::__construct($response);
      
      if (NULL === $dataCaster) {
        $this->dataCaster= new RestDataCaster();
      } else {
        $this->dataCaster= $dataCaster;
      }
    }

    /**
     * Set body
     * 
     * @param var[] data The data
     */
    public function setData($data) {
      $this->response->setHeader('Content-Type', 'application/json');
      $this->response->write(create(new JsonDecoder())->encode($this->dataCaster->simple($data)));
    }
  }
?>
