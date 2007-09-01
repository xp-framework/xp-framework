<?php
/* This class is part of the XP framework
 *
 * $Id: FlickrInterestingnessResponse.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace com::flickr::api;

  ::uses(
    'lang.Collection',
    'com.flickr.FlickrPhotoCollection'
  );

  /**
   * Response to Interestingness query
   *
   * @purpose  Value object
   */
  class FlickrInterestingnessResponse extends lang::Object {
    public
      $photos   =  NULL;
    
    /**
     * Constructor
     *
     */
    public function __construct() {
      $this->photos= lang::Collection::forClass('com.flickr.FlickrPhoto');
    }
    
    /**
     * Add a photo
     *
     * @param   &com.flickr.FlickrPhoto photo
     */
    #[@xmlmapping(element= 'photo', class= 'com.flickr.FlickrPhoto')]
    public function addPhoto($photo) {
      $this->photos->add($photo);
    }
  }
?>
