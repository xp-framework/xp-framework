<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'io.streams.Streams', 
    'io.streams.MemoryInputStream',
    'peer.http.HttpResponse'
  );

  /**
   * A REST response
   *
   * @test    xp://net.xp_framework.unittest.webservices.rest.RestResponseTest
   */
  class RestResponse extends Object {
    protected $response= NULL;
    protected $deserializer= NULL;
    protected $type= NULL;
    protected $input= NULL;

    /**
     * Creates a new response
     *
     * @param   peer.http.HttpResponse response
     * @param   webservices.rest.RestDeserializer deserializer
     * @param   lang.Type type
     */
    public function __construct(HttpResponse $response, RestDeserializer $deserializer= NULL, Type $type= NULL) {
      $this->response= $response;
      $this->deserializer= $deserializer;
      $this->type= $type;
      $this->input= $response->getInputStream();
    }

    /**
     * Get status code
     *
     * @return  int
     */
    public function status() {
      return $this->response->statusCode();
    }

    /**
     * Get status message
     *
     * @return  string
     */

    public function message() {
      return $this->response->message();
    }

    /**
     * Get data
     *
     * @return  string
     */
    public function content() {
      return Streams::readAll($this->input);
    }

    /**
     * Get data as stream
     *
     * @return  io.streams.InputStream
     */
    public function stream() {
      return $this->input;
    }

    /**
     * Get headers
     *
     * @return  [:var]
     */
    public function headers() {
      $r= array();
      foreach ($this->response->headers() as $key => $values) {
        $r[$key]= sizeof($values) > 1 ? $values : $values[0];
      }
      return $r;
    }

    /**
     * Get header by a specified name
     *
     * @param   string name
     * @return  var
     */
    public function header($name) {
      if (NULL === ($values= $this->response->header($name))) return NULL;  // Not found
      return sizeof($values) > 1 ? $values : $values[0];
   }
    
    /**
     * Copy data
     *
     * @return  string
     */
    public function contentCopy() {
      $data= $this->content();

      // Reassign input, so code relying on the stream delivering bytes
      // can still read them.
      $this->input= new MemoryInputStream($data);
      return $data;
    }

    /**
     * Handle status code. Throws an exception in this default implementation
     * if the numeric value is larger than 399. Overwrite in subclasses to 
     * change this behaviour.
     *
     * @param   int code
     * @throws  webservices.rest.RestException
     */
    protected function handleStatus($code) {
      if ($code > 399) {
        throw new RestException($code.': '.$this->response->message());
      }
    }

    /**
     * Handle payload deserialization. Uses the deserializer passed to the
     * constructor to deserialize the input stream and coerces it to the 
     * passed target type. Overwrite in subclasses to change this behaviour.
     *
     * @param   lang.Type target
     * @return  var
     */
    protected function handlePayloadOf($target) {
      return $this->deserializer->deserialize($this->input, $target);
    }

    /**
     * Get data
     *
     * @param   var type target type of deserialization, either a lang.Type or a string
     * @return  var
     * @throws  webservices.rest.RestException if the status code is > 399
     */
    public function data($type= NULL) {
      $this->handleStatus($this->response->statusCode());
 
      if (NULL === $type) {
        $target= $this->type;  // BC
      } else if ($type instanceof Type) {
        $target= $type;
      } else {
        $target= Type::forName($type);
      }

      if (NULL === $this->deserializer) {
        throw new IllegalArgumentException('Unknown content type "'.$this->headers['Content-Type'][0].'"');
      }

      return $this->handlePayloadOf($target);
    }

    /**
     * Creates a string representation
     *
     * @return string
     */
    public function toString() {
      return $this->getClassName().'<'.$this->response->message().'>@(->'.$this->response->toString().')';
    }
  }
?>
