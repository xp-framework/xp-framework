<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.URL',
    'peer.http.HttpRequest'
  );

  /**
   * Request factory. Used internally by the HttpConnection class.
   *
   * @see      xp://peer.http.HttpConnection
   * @purpose  Factory for HTTP / HTTPS
   */
  class HttpRequestFactory extends Object {
  
    /**
     * Factory method
     *
     * @param   peer.URL an url object
     * @return  lang.Object a request object
     * @throws  lang.IllegalArgumentException in case the scheme is not supported
     */
    public static function factory($url) {
      switch ($url->getScheme()) {
        case 'http':
          return new HttpRequest($url);
          
        case 'https':
          return new HttpsRequest($url);
        
        default:
          throw new IllegalArgumentException('Scheme "'.$url->getScheme().'" not supported');
      }
    }
  }
?>
