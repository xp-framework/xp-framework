<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'webservices.rest.srv';

  uses('scriptlet.Cookie');

  /**
   * Represents output
   *
   */
  abstract class webservices·rest·srv·Output extends Object {
    public $status;
    public $headers= array();
    public $cookies= array();

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
     * Write response headers
     *
     * @param  scriptlet.Response response
     * @param  peer.URL base
     * @param  string format
     */
    protected abstract function writeHead($response, $base, $format);

    /**
     * Write response body
     *
     * @param  scriptlet.Response response
     * @param  peer.URL base
     * @param  string format
     */
    protected abstract function writeBody($response, $base, $format);

    /**
     * Write this output to the scriptlet's response
     *
     * @param  scriptlet.Response response
     * @param  peer.URL base
     * @param  string format
     * @return bool handled
     */
    public function writeTo($response, $base, $format) {
      $response->setStatus($this->status);
      $this->writeHead($response, $base, $format);

      // Headers
      foreach ($this->headers as $name => $value) {
        if ('Location' === $name) {
          $url= clone $base;
          $response->setHeader($name, $url->setPath($value)->getURL());
        } else {
          $response->setHeader($name, $value);
        }
      }
      foreach ($this->cookies as $cookie) {
        $response->setCookie($cookie);
      }

      $this->writeBody($response, $base, $format);
      return TRUE;
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
      return (
        $cmp instanceof self &&
        $this->status === $cmp->status &&
        $this->arrayequals($this->headers, $cmp->headers) &&
        $this->arrayequals($this->cookies, $cmp->cookies)
      );
    }
  }
?>
