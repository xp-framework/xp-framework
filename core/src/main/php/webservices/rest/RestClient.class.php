<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.log.Traceable',
    'peer.Header',
    'peer.http.HttpConnection',
    'webservices.rest.RestRequest',
    'webservices.rest.RestResponse',
    'webservices.rest.RestXmlDeserializer',
    'webservices.rest.RestJsonDeserializer',
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
    protected $deserializers= array();
    
    /**
     * Creates a new Restconnection instance
     *
     * @param   var base default NULL
     */
    public function __construct($base= NULL) {
      if (NULL !== $base) $this->setBase($base);

      $this->deserializers['application/xml']= new RestXmlDeserializer();
      $this->deserializers['application/json']= new RestJsonDeserializer();
      $this->deserializers['text/xml']= $this->deserializers['application/xml'];
      $this->deserializers['text/json']= $this->deserializers['application/json'];
      $this->deserializers['text/x-json']= $this->deserializers['application/json'];
      $this->deserializers['text/javascript']= $this->deserializers['application/json'];
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
     * Sets deserializer
     *
     * @param   string mediaType e.g. "text/xml"
     * @param   webservices.rest.Deserializer deserializer
     */
    public function setDeserializer($mediaType, $deserializer) {
      $this->deserializers[$mediaType]= $deserializer;
    }
    
    /**
     * Returns a deserializer
     *
     * @param   string contentType
     * @return  webservices.rest.RestDeserializer
     */
    public function deserializerFor($contentType) {
      $mediaType= substr($contentType, 0, strcspn($contentType, ';'));
      return isset($this->deserializers[$mediaType])
        ? $this->deserializers[$mediaType]
        : NULL
      ;
    }

    /**
     * Execute a request
     *
     * @param   var t either a string or a lang.Type - response type, defaults to webservices.rest.RestResponse
     * @param   webservices.rest.RestRequest request
     * @return  webservices.rest.RestResponse
     * @throws  lang.IllegalStateException if no connection is set
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

      if (NULL === $this->connection) {
        throw new IllegalStateException('No connection set');
      }

      $send= $this->connection->create(new HttpRequest());
      $send->addHeaders($request->headerList());
      $send->setMethod($request->getMethod());
      $send->setTarget($request->getTarget($this->connection->getUrl()->getPath('/')));
      
      $send->setParameters($request->getParameters());
      if ($request->hasBody()) {
        $send->setBody($request->getBody());
      }
      
      try {
        $this->cat && $this->cat->debug('>>>', $send->getRequestString());
        $response= $this->connection->send($send);
      } catch (IOException $e) {
        throw new RestException('Cannot send request', $e);
      }

      if ($type instanceof XPClass && $type->isSubclassOf('webservices.rest.RestResponse')) {
        $rr= $type->newInstance(
          $response,
          $this->deserializerFor(this($response->header('Content-Type'), 0))
        );
      } else {
        $rr= new RestResponse(
          $response,
          $this->deserializerFor(this($response->header('Content-Type'), 0)),
          $type       // Deprecated: This should be done via $response->data($type)
        );
      }

      $this->cat && $this->cat->debug('<<<', $response->toString(), $rr->contentCopy());
      return $rr;
    }
  }
?>
