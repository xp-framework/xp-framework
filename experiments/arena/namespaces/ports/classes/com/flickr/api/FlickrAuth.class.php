<?php
/* This class is part of the XP framework
 *
 * $Id: FlickrAuth.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace com::flickr::api;

  ::uses('peer.URL');

  /**
   * FlickR authentication
   *
   * @purpose  Authentication
   */
  class FlickrAuth extends lang::Object {
    public
      $frob     = '',
      $token    = '';

    /**
     * Set Frob
     *
     * @param   string frob
     */
    public function setFrobValue($frob) {
      $this->frob= $frob;
    }

    /**
     * Get Frob
     *
     * @return  string
     */
    public function getFrobValue() {
      return $this->frob;
    }

    /**
     * Set Token
     *
     * @param   string token
     */
    public function setTokenValue($token) {
      $this->token= $token;
    }

    /**
     * Get Token
     *
     * @return  string
     */
    public function getTokenValue() {
      return $this->token;
    }

    /**
     * Get FROB
     *
     * @param   &com.flickr.xmlrpc.Client client
     */
    public function getFrob($client) {
      $res= $client->invoke('flickr.auth.getFrob', array(
        'perms' => 'read'
      ));
      $this->setFrobValue($res['frob']);
    }

    /**
     * Get FROB URL
     *
     * @param   &com.flickr.xmlrpc.Client client
     * @return  string url
     */
    public function getFrobURL($client) {
      $arguments= array(
        'frob'  => $this->getFrobValue(),
        'perms' => 'read'
      );
      $arguments= $client->signArray($arguments);
      
      $url= new peer::URL('http://flickr.com/services/auth');
      $url->addParams($arguments);
      
      return $url->getURL();
    }
    
    /**
     * Get token
     *
     * @param   &com.flickr.xmlrpc.Client client
     * @return  mixed
     */
    public function getToken($client) {
      $res= $client->invoke('flickr.auth.getToken', array(
        'frob'  => $this->getFrobValue()
      ));
      
      return $res;
    }    
  }
?>
