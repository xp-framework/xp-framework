<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.URL', 'peer.http.HttpRequest', 'peer.http.HttpsRequest');

  /**
   * (Insert class' description here)
   *
   * @ext      extensiom
   * @see      reference
   * @purpose  purpose
   */
  class HttpRequestFactory extends Object {
  
    /**
     * Factory method
     *
     * @access  
     * @param   
     * @return  
     * @throws  Exception
     */
    function factory(&$url) {
      if (!is_a($url, 'URL')) $url= &new URL($url);
      switch ($url->getScheme()) {
        case 'http':
          return new HttpRequest($url);
          
        case 'https':
          return new HttpsRequest($url);
        
        default:
          throw(new Exception('Scheme "'.$url->getScheme().'" not supported'));
      }
    }
  }
?>
