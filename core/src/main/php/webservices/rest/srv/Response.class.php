<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

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
  class Response extends Object {
    public $status;
    public $headers= array();
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
     * Adds a header and returns this instance
     * 
     * @param   string name
     * @param   string value
     * @return  self
     */
    public function withHeader($name, $value) {
      $this->headers[$name]= $value;
      return $this;
    }

    /**
     * Sets payload and returns this instance
     * 
     * @param   var data
     * @return  self
     */
    public function withPayload($data) {
      $this->payload= $data;
      return $this;
    }

    /**
     * Helper method to compare two arrays recursively
     *
     * @param   array a1
     * @param   array a2
     * @return  bool
     */
    protected function arrayequals($a1, $a2) {
      if (sizeof($a1) != sizeof($a2)) return FALSE;

      foreach (array_keys((array)$a1) as $k) {
        switch (TRUE) {
          case !array_key_exists($k, $a2): 
            return FALSE;

          case is_array($a1[$k]):
            if (!$this->arrayequals($a1[$k], $a2[$k])) return FALSE;
            break;

          case $a1[$k] instanceof Generic:
            if (!$a1[$k]->equals($a2[$k])) return FALSE;
            break;

          case $a1[$k] !== $a2[$k]:
            return FALSE;
        }
      }
      return TRUE;
    }

    /**
     * Returns whether a given value is equal to this Response instance
     *
     * @param  var cmp
     * @return bool
     */
    public function equals($cmp) {
      if (!$cmp instanceof self || $this->status !== $cmp->status) return FALSE;

      // Compare payload
      if ($this->payload instanceof Generic) {
        if (!$this->payload->equals($cmp->payload)) return FALSE;
      } else if (is_array($this->payload)) {
        if (!$this->arrayequals($this->payload, $cmp->payload)) return FALSE;
      } else {
        if ($this->payload !== $cmp->payload) return FALSE;
      }

      return $this->arrayequals($this->headers, $cmp->headers);
    }
  }
?>