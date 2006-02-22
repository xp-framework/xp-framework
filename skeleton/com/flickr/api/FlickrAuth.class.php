<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.URL');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class FlickrAuth extends Object {
    var
      $frob     = '',
      $token    = '';

    /**
     * Set Frob
     *
     * @access  public
     * @param   string frob
     */
    function setFrobValue($frob) {
      $this->frob= $frob;
    }

    /**
     * Get Frob
     *
     * @access  public
     * @return  string
     */
    function getFrobValue() {
      return $this->frob;
    }

    /**
     * Set Token
     *
     * @access  public
     * @param   string token
     */
    function setTokenValue($token) {
      $this->token= $token;
    }

    /**
     * Get Token
     *
     * @access  public
     * @return  string
     */
    function getTokenValue() {
      return $this->token;
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function getFrob(&$client) {
      $res= $client->invoke('flickr.auth.getFrob', array(
        'perms' => 'read'
      ));
      $this->setFrobValue($res['frob']);
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function getFrobURL(&$client) {
      $arguments= array(
        'frob'  => $this->getFrobValue(),
        'perms' => 'read'
      );
      $arguments= $client->signArray($arguments);
      
      $url= &new URL('http://flickr.com/services/auth');
      $url->addParams($arguments);
      
      return $url->getURL();
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function getToken(&$client) {
      $res= $client->invoke('flickr.auth.getToken', array(
        'frob'  => $this->getFrobValue()
      ));
      
      return $res;
    }    
  }
?>
