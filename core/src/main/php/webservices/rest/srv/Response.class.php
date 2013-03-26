<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('webservices.rest.srv.Output', 'webservices.rest.Payload');

  /**
   * The Response class can be used to control the HTTP status code and headers
   * of a REST call.
   *
   * <code>
   *   #[@webservice(verb= 'POST', path= '/resources')]
   *   public function addElement(Element $element) {
   *     // TBI: Create element
   *     return Response::created();
   *   }
   * </code>
   *
   * @test  xp://net.xp_framework.unittest.webservices.rest.srv.ResponseTest
   */
  class Response extends webservices·rest·srv·Output {
    public $payload;

    /**
     * Creates a new response instance
     *
     * @param  int status
     */
    public function __construct($status= NULL) {
      $this->status= $status;
    }

    /**
     * Creates a new response instance with the status code set to 200 (OK)
     *
     * @return  self
     */
    public static function ok() {
      $self= new self();
      $self->status= 200;
      return $self;
    }

    /**
     * Creates a new response instance with the status code set to 201 (Created)
     * and an optional location.
     *
     * @param   string location
     * @return  self
     */
    public static function created($location= NULL) {
      $self= new self();
      $self->status= 201;
      if (NULL !== $location) $self->headers['Location']= $location;
      return $self;
    }

    /**
     * Creates a new response instance with the status code set to 204 (No content)
     *
     * @return  self
     */
    public static function noContent() {
      $self= new self();
      $self->status= 204;
      return $self;
    }

    /**
     * Creates a new response instance with the status code set to 302 (See other)
     * and a specified location.
     *
     * @param   string location
     * @return  self
     */
    public static function see($location) {
      $self= new self();
      $self->status= 302;
      $self->headers['Location']= $location;
      return $self;
    }

    /**
     * Creates a new response instance with the status code set to 304 (Not modified)
     *
     * @return  self
     */
    public static function notModified() {
      $self= new self();
      $self->status= 304;
      return $self;
    }

    /**
     * Creates a new response instance with the status code set to 404 (Not found)
     *
     * @return  self
     */
    public static function notFound() {
      $self= new self();
      $self->status= 404;
      return $self;
    }

    /**
     * Creates a new response instance with the status code set to 406 (Not acceptable)
     *
     * @return  self
     */
    public static function notAcceptable() {
      $self= new self();
      $self->status= 406;
      return $self;
    }

    /**
     * Creates a new response instance with the status code set to a given status.
     *
     * @param   int code
     * @return  self
     */
    public static function status($code) {
      $self= new self();
      $self->status= $code;
      return $self;
    }

    /**
     * Creates a new response instance with the status code optionally set to a given
     * error code (defaulting to 500 - Internal Server Error).
     *
     * @param   int code
     * @return  self
     */
    public static function error($code= 500) {
      $self= new self();
      $self->status= $code;
      return $self;
    }

    /**
     * Sets payload and returns this instance
     * 
     * @param   var data
     * @return  self
     */
    public function withPayload($data) {
      if ($data instanceof Payload) {
        $this->payload= $data;
      } else {
        $this->payload= new Payload($data);
      }
      return $this;
    }

    /**
     * Write response headers
     *
     * @param  scriptlet.Response response
     * @param  peer.URL base
     * @param  string format
     */
    protected function writeHead($response, $base, $format) {
      if (NULL !== $this->payload && !isset($this->headers['Content-Type'])) {
        $response->setContentType($format);
      }
    }

    /**
     * Write response body
     *
     * @param  scriptlet.Response response
     * @param  peer.URL base
     * @param  string format
     */
    protected function writeBody($response, $base, $format) {
      if (NULL !== $this->payload) {
        RestFormat::forMediaType($format)->write($response->getOutputStream(), $this->payload);
      }
    }

    /**
     * Returns whether a given value is equal to this Response instance
     *
     * @param  var cmp
     * @return bool
     */
    public function equals($cmp) {
      return (
        parent::equals($cmp) &&
        (NULL === $this->payload ? NULL === $cmp->payload : $this->payload->equals($cmp->payload))
      );
    }
  }
?>