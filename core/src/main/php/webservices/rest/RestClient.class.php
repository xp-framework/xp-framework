<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.log.Traceable',
    'peer.http.HttpConnection',
    'webservices.rest.RestRequest',
    'webservices.rest.RestResponse',
    'webservices.rest.RestException'
  );

  /**
   * REST client
   *
   * @test    xp://net.xp_framework.unittest.webservices.rest.RestClientTest
   * @test    xp://net.xp_framework.unittest.webservices.rest.RestClientSendTest
   * @test    xp://net.xp_framework.unittest.webservices.rest.RestClientExecutionTest
   */
  class RestClient extends Object implements Traceable {
    protected $connection= NULL;
    protected $cat= NULL;
    
    /**
     * Creates a new Restconnection instance
     *
     * @param   var base default NULL
     */
    public function __construct($base= NULL) {
      if (NULL !== $base) $this->setBase($base);
    }

    /**
     * Set trace
     *
     * @param   util.log.LogCategory cat
     */
    public function setTrace($cat) {
      $this->cat= $cat;
    }

    /**
     * Sets base
     *
     * @param   var base either a peer.URL or a string
     */
    public function setBase($base) {
      $this->setConnection(new HttpConnection($base));
    }
    
    /**
     * Sets base and returns this connection
     *
     * @param   var base either a peer.URL or a string
     * @return  webservices.rest.Restconnection
     */
    public function withBase($base) {
      $this->setBase($base);
      return $this;
    }
    
    /**
     * Get base
     *
     * @return  peer.URL
     */
    public function getBase() {
      return $this->connection ? $this->connection->getURL() : NULL;
    }
    
    /**
     * Sets HTTP connection
     *
     * @param   peer.http.HttpConnection connection
     */
    public function setConnection(HttpConnection $connection) {
      $this->connection= $connection;
    }

    /**
     * Execute a request
     *
     * @return  webservices.rest.RestResponse
     */
    public function execute() {
      $args= func_get_args();
      if (is_string($args[0])) {
        $type= Type::forName($args[0]);
        $offset= 1;
      } else if ($args[0] instanceof Type) {
        $type= $args[0];
        $offset= 1;
      } else {
        $type= NULL;
        $offset= 0;
      }

      $request= $args[$offset];
      $send= $this->connection->create(new HttpRequest());
      $send->addHeaders($request->getHeaders());
      $send->setMethod($request->getMethod());
      $send->setTarget($request->getTarget());
      
      if ($request->hasBody()) {
        $send->setParameters($request->getBody());
      } else {
        $send->setParameters($request->getParameters());
      }
      
      try {
        $this->cat && $this->cat->debug('>>>', $send->getRequestString());
        $response= $this->connection->send($send);
      } catch (IOException $e) {
        throw new RestException('Cannot send request', $e);
      }
      
      $rr= new RestResponse(
        $response->statusCode(), 
        this($response->header('Content-Type'), 0),
        $response->headers(), 
        $type,
        $response->getInputStream()
      );

      $this->cat && $this->cat->debug('<<<', $response->toString(), $rr->contentCopy());
      return $rr;
    }
  }
?>
