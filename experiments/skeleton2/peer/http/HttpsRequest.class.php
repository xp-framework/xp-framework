<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.http.HttpRequest',
    'peer.http.HttpsResponse'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      curl
   * @see      reference
   * @purpose  purpose
   */
  class HttpsRequest extends HttpRequest {
  
    /**
     * Send request
     *
     * @access  public
     * @return  &peer.http.HttpsResponse response object
     */
    public function send() {
      $curl= curl_init($this->url->getURL());
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, self::getRequestString());
      curl_setopt($curl, CURLOPT_HEADER, 1);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
      
      if (FALSE === ($ret= curl_exec($curl))) {
        throw (new IOException(sprintf('%d: %s', curl_errno($curl), curl_error($curl))));
      }
      
      return new HttpsResponse(array($curl, $ret));
    }
  
  }
?>
