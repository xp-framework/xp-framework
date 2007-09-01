<?php
/* This class is part of the XP framework
 *
 * $Id: FlickrPhotoSizes.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace com::flickr;

  ::uses(
    'lang.Collection',
    'com.flickr.FlickrPhotoSize'
  );

  /**
   * Container with all sizes of a picture.
   *
   * @see      xp://com.flickr.FlickrPhotoSize
   * @purpose  Container
   */
  class FlickrPhotoSizes extends lang::Object {
    public
      $sizes  = NULL;
    
    /**
     * Constructor
     *
     */
    public function __construct() {
      $this->sizes= lang::Collection::forClass('com.flickr.FlickrPhotoSize');
    }
    
    /**
     * Set Client
     *
     * @param   &com.flickr.xmlrpc.FlickrClient client
     */
    public function setClient($client) {
    }
      
    /**
     * Add new size
     *
     * @param   &lang.Object size
     */
    #[@xmlmapping(element= 'size', class= 'com.flickr.FlickrPhotoSize')]
    public function addSize($size) {
      $this->sizes->add($size);
      return $size;
    }
  }
?>
