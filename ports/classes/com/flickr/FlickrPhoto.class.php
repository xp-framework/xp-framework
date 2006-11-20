<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.URL');

  /**
   * Flickr photo
   *
   * @purpose  Flickr photo
   */
  class FlickrPhoto extends Object {
    var
      $id       = '',
      $owner    = '',
      $secret   = '',
      $server   = '',
      $title    = '',
      $isPublic = TRUE,
      $isFriend = TRUE,
      $isFamily = TRUE;
    
    var
      $_client   = NULL;

    /**
     * Set Client
     *
     * @access  public
     * @param   &com.flickr.xmlrpc.FlickrClient client
     */
    function setClient(&$client) {
      $this->_client= &$client;
    }

    /**
     * Set Id
     *
     * @access  public
     * @param   string id
     */
    #[@xmlmapping(element= '@id')]
    function setId($id) {
      $this->id= $id;
    }

    /**
     * Get Id
     *
     * @access  public
     * @return  string
     */
    function getId() {
      return $this->id;
    }

    /**
     * Set Owner
     *
     * @access  public
     * @param   string owner
     */
    #[@xmlmapping(element= '@owner')]
    function setOwner($owner) {
      $this->owner= $owner;
    }

    /**
     * Get Owner
     *
     * @access  public
     * @return  string
     */
    function getOwner() {
      return $this->owner;
    }

    /**
     * Set Secret
     *
     * @access  public
     * @param   string secret
     */
    #[@xmlmapping(element= '@secret')]
    function setSecret($secret) {
      $this->secret= $secret;
    }

    /**
     * Get Secret
     *
     * @access  public
     * @return  string
     */
    function getSecret() {
      return $this->secret;
    }

    /**
     * Set Server
     *
     * @access  public
     * @param   string server
     */
    #[@xmlmapping(element= '@server')]
    function setServer($server) {
      $this->server= $server;
    }

    /**
     * Get Server
     *
     * @access  public
     * @return  string
     */
    function getServer() {
      return $this->server;
    }

    /**
     * Set Title
     *
     * @access  public
     * @param   string title
     */
    #[@xmlmapping(element= '@title')]
    function setTitle($title) {
      $this->title= $title;
    }

    /**
     * Get Title
     *
     * @access  public
     * @return  string
     */
    function getTitle() {
      return $this->title;
    }

    /**
     * Set IsPublic
     *
     * @access  public
     * @param   bool isPublic
     */
    #[@xmlmapping(element= '@isPublic', type= 'boolean')]
    function setIsPublic($isPublic) {
      $this->isPublic= $isPublic;
    }

    /**
     * Get IsPublic
     *
     * @access  public
     * @return  bool
     */
    function getIsPublic() {
      return $this->isPublic;
    }

    /**
     * Set IsFriend
     *
     * @access  public
     * @param   bool isFriend
     */
    #[@xmlmapping(element= '@isFriend', type= 'boolean')]
    function setIsFriend($isFriend) {
      $this->isFriend= $isFriend;
    }

    /**
     * Get IsFriend
     *
     * @access  public
     * @return  bool
     */
    function getIsFriend() {
      return $this->isFriend;
    }

    /**
     * Set IsFamily
     *
     * @access  public
     * @param   bool isFamily
     */
    #[@xmlmapping(element= '@isFamily', type= 'boolean')]
    function setIsFamily($isFamily) {
      $this->isFamily= $isFamily;
    }

    /**
     * Get IsFamily
     *
     * @access  public
     * @return  bool
     */
    function getIsFamily() {
      return $this->isFamily;
    }
    
    /**
     * Calculate URL for this photo
     *
     * @access  public
     * @return  &peer.URL
     */
    function &getURL() {
      $url= &new URL(sprintf('http://static.flickr.com/%s/%s_%s_b.jpg',
        $this->getServer(),
        $this->getId(),
        $this->getSecret()
      ));
      return $url;
    }
    
    /**
     * Fetch available sizes of this picture
     *
     * @access  public
     * @return  com.flickr.FlicksPhotoSizes
     */
    function getSizes() {
      return $this->_client->invokeExpecting(
        'flickr.photos.getSizes',
        array('photo_id'  => $this->getId()),
        'com.flickr.FlickrPhotoSizes'
      );
    }
    
    /**
     * Builds the string representation of this object
     *
     * @access  public
     * @return  string
     */
    function toString() {
      $s= $this->getClassName().'@('.$this->__id.") {\n";
      foreach (get_object_vars($this) as $key => $value) {
        if ('_' == $key{0}) continue;
        
        $s.= sprintf('  [%10s] %s', $key, $value)."\n";
      }
      return $s.'}';
    }    
  }
?>
