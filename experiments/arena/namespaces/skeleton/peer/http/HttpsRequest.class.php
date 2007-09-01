<?php
/* This class is part of the XP framework
 *
 * $Id: HttpsRequest.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace peer::http;

  ::uses('peer.http.HttpRequest', 'peer.http.HttpsResponse');

  /**
   * Wrap HTTPS requests (user internally by the HttpConnection class)
   *
   * @ext      curl
   * @see      xp://peer.http.HttpConnection
   * @purpose  HTTP request
   */
  class HttpsRequest extends HttpRequest {
  
    /**
     * Send request
     *
     * @return  peer.http.HttpsResponse response object
     */
    public function send($timeout= 60) {
      $curl= curl_init($this->url->getURL());
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $this->getRequestString());
      curl_setopt($curl, CURLOPT_HEADER, 1);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
      
      if (FALSE === ($ret= curl_exec($curl))) {
        throw(new io::IOException(sprintf('%d: %s', curl_errno($curl), curl_error($curl))));
      }
      
      return new HttpsResponse(array($curl, $ret));
    }
  
  }
?>
