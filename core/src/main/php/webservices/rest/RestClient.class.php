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
     * @param   var t either a string or a lang.Type - target type for payload
     * @param   webservices.rest.RestRequest request
     * @return  webservices.rest.RestResponse
     */
    public function execute($t, $request= NULL) {
      if (1 === func_num_args()) {      // Overloaded version with single argument
        $request= $t;
        $type= NULL;
      } else if (is_string($t)) {       // Overloaded version with string type
        $type= Type::forName($t);
      } else if ($t instanceof Type) {  // Overloaded version with Type instance
        $type= $t;
      } else {
        throw new IllegalArgumentException('Given type is neither a Type nor a string, '.xp::typeOf($request).' given');
      }
      if (!$request instanceof RestRequest) {
        throw new IllegalArgumentException('Given request is not a RestRequest, '.xp::typeOf($request).' given');
      }

      $send= $this->connection->create(new HttpRequest());
      $send->addHeaders($request->getHeaders());
      $send->setMethod($request->getMethod());
      $send->setTarget($request->getTarget($this->connection->getUrl()->getPath('/')));
      
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
