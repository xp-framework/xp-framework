<?php
/* This class is part of the XP framework
 *
 * $Id: FlickrPhoto.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace com::flickr;

  ::uses('peer.URL');

  /**
   * Flickr photo
   *
   * @purpose  Flickr photo
   */
  class FlickrPhoto extends lang::Object {
    public
      $id       = '',
      $owner    = '',
      $secret   = '',
      $server   = '',
      $title    = '',
      $isPublic = TRUE,
      $isFriend = TRUE,
      $isFamily = TRUE;
    
    public
      $_client   = NULL;

    /**
     * Set Client
     *
     * @param   &com.flickr.xmlrpc.FlickrClient client
     */
    public function setClient($client) {
      $this->_client= $client;
    }

    /**
     * Set Id
     *
     * @param   string id
     */
    #[@xmlmapping(element= '@id')]
    public function setId($id) {
      $this->id= $id;
    }

    /**
     * Get Id
     *
     * @return  string
     */
    public function getId() {
      return $this->id;
    }

    /**
     * Set Owner
     *
     * @param   string owner
     */
    #[@xmlmapping(element= '@owner')]
    public function setOwner($owner) {
      $this->owner= $owner;
    }

    /**
     * Get Owner
     *
     * @return  string
     */
    public function getOwner() {
      return $this->owner;
    }

    /**
     * Set Secret
     *
     * @param   string secret
     */
    #[@xmlmapping(element= '@secret')]
    public function setSecret($secret) {
      $this->secret= $secret;
    }

    /**
     * Get Secret
     *
     * @return  string
     */
    public function getSecret() {
      return $this->secret;
    }

    /**
     * Set Server
     *
     * @param   string server
     */
    #[@xmlmapping(element= '@server')]
    public function setServer($server) {
      $this->server= $server;
    }

    /**
     * Get Server
     *
     * @return  string
     */
    public function getServer() {
      return $this->server;
    }

    /**
     * Set Title
     *
     * @param   string title
     */
    #[@xmlmapping(element= '@title')]
    public function setTitle($title) {
      $this->title= $title;
    }

    /**
     * Get Title
     *
     * @return  string
     */
    public function getTitle() {
      return $this->title;
    }

    /**
     * Set IsPublic
     *
     * @param   bool isPublic
     */
    #[@xmlmapping(element= '@isPublic', type= 'boolean')]
    public function setIsPublic($isPublic) {
      $this->isPublic= $isPublic;
    }

    /**
     * Get IsPublic
     *
     * @return  bool
     */
    public function getIsPublic() {
      return $this->isPublic;
    }

    /**
     * Set IsFriend
     *
     * @param   bool isFriend
     */
    #[@xmlmapping(element= '@isFriend', type= 'boolean')]
    public function setIsFriend($isFriend) {
      $this->isFriend= $isFriend;
    }

    /**
     * Get IsFriend
     *
     * @return  bool
     */
    public function getIsFriend() {
      return $this->isFriend;
    }

    /**
     * Set IsFamily
     *
     * @param   bool isFamily
     */
    #[@xmlmapping(element= '@isFamily', type= 'boolean')]
    public function setIsFamily($isFamily) {
      $this->isFamily= $isFamily;
    }

    /**
     * Get IsFamily
     *
     * @return  bool
     */
    public function getIsFamily() {
      return $this->isFamily;
    }
    
    /**
     * Calculate URL for this photo
     *
     * @return  &peer.URL
     */
    public function getURL() {
      $url= new peer::URL(sprintf('http://static.flickr.com/%s/%s_%s_b.jpg',
        $this->getServer(),
        $this->getId(),
        $this->getSecret()
      ));
      return $url;
    }
    
    /**
     * Fetch available sizes of this picture
     *
     * @return  com.flickr.FlicksPhotoSizes
     */
    public function getSizes() {
      return $this->_client->invokeExpecting(
        'flickr.photos.getSizes',
        array('photo_id'  => $this->getId()),
        'com.flickr.FlickrPhotoSizes'
      );
    }
    
    /**
     * Builds the string representation of this object
     *
     * @return  string
     */
    public function toString() {
      $s= $this->getClassName().'@('.$this->__id.") {\n";
      foreach (get_object_vars($this) as $key => $value) {
        if ('_' == $key{0}) continue;
        
        $s.= sprintf('  [%10s] %s', $key, $value)."\n";
      }
      return $s.'}';
    }    
  }
?>
