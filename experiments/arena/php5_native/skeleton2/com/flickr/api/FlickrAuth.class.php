<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.URL');

  /**
   * FlickR authentication
   *
   * @purpose  Authentication
   */
  class FlickrAuth extends Object {
    public
      $frob     = '',
      $token    = '';

    /**
     * Set Frob
     *
     * @access  public
     * @param   string frob
     */
    public function setFrobValue($frob) {
      $this->frob= $frob;
    }

    /**
     * Get Frob
     *
     * @access  public
     * @return  string
     */
    public function getFrobValue() {
      return $this->frob;
    }

    /**
     * Set Token
     *
     * @access  public
     * @param   string token
     */
    public function setTokenValue($token) {
      $this->token= $token;
    }

    /**
     * Get Token
     *
     * @access  public
     * @return  string
     */
    public function getTokenValue() {
      return $this->token;
    }

    /**
     * Get FROB
     *
     * @access  public
     * @param   &com.flickr.xmlrpc.Client client
     */
    public function getFrob(&$client) {
      $res= $client->invoke('flickr.auth.getFrob', array(
        'perms' => 'read'
      ));
      $this->setFrobValue($res['frob']);
    }

    /**
     * Get FROB URL
     *
     * @access  public
     * @param   &com.flickr.xmlrpc.Client client
     * @return  string url
     */
    public function getFrobURL(&$client) {
      $arguments= array(
        'frob'  => $this->getFrobValue(),
        'perms' => 'read'
      );
      $arguments= $client->signArray($arguments);
      
      $url= new URL('http://flickr.com/services/auth');
      $url->addParams($arguments);
      
      return $url->getURL();
    }
    
    /**
     * Get token
     *
     * @access  public
     * @param   &com.flickr.xmlrpc.Client client
     * @return  mixed
     */
    public function getToken(&$client) {
      $res= $client->invoke('flickr.auth.getToken', array(
        'frob'  => $this->getFrobValue()
      ));
      
      return $res;
    }    
  }
?>
