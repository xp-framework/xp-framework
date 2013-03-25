<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'webservices.rest';

  /**
   * Represents output
   *
   */
  abstract class webservices·rest·Output extends Object {
    protected $status;
    protected $headers= array();
    protected $cookies= array();

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
     * Adds a cookie and returns this instance
     *
     * @param   scriptlet.Cookie cookie
     * @return  self
     */
    public function withCookie(Cookie $cookie) {
      $this->cookies[]= $cookie;
      return $this;
    }

    /**
     * Writes this payload to an output stream
     *
     * @param  scriptlet.Response response
     * @param  peer.URL base
     * @param  string format
     * @return bool handled
     */
    public abstract function writeTo($response, $base, $format);
  }
?>
