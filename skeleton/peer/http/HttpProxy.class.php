<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * HTTP proxy
   *
   * @see      xp://peer.http.HttpConnection#setProxy
   * @purpose  Value object
   */
  class HttpProxy extends Object {
    public
      $host     = '',
      $port     = 0,
      $excludes = array('localhost');
    
    /**
     * Constructor
     *
     * @param   string host
     * @param   int port default 8080
     */
    public function __construct($host, $port= 8080) {
      $this->host= $host;
      $this->port= $port;
    }

    /**
     * Add a URL pattern to exclude.
     *
     * @param   string pattern
     */
    public function addExclude($pattern) {
      $this->excludes[]= $pattern;
    }
    
    /**
     * Add a URL pattern to exclude and return this proxy. For use with
     * chained method calls.
     *
     * @param   string pattern
     * @return  peer.http.HttpProxy this object
     */
    public function withExclude($pattern) {
      $this->excludes[]= $pattern;
      return $this;
    }

    /**
     * Check whether a given URL is excluded
     *
     * @param   peer.URL url
     * @return  bool
     */
    public function isExcluded(URL $url) {
      foreach ($this->excludes as $pattern) {
        if (stristr($url->getHost(), $pattern)) return TRUE;
      }
      return FALSE;
    }
  }
?>
