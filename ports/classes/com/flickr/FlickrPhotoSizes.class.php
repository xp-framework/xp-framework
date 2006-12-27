<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.Collection',
    'com.flickr.FlickrPhotoSize'
  );

  /**
   * Container with all sizes of a picture.
   *
   * @see      xp://com.flickr.FlickrPhotoSize
   * @purpose  Container
   */
  class FlickrPhotoSizes extends Object {
    public
      $sizes  = NULL;
    
    /**
     * Constructor
     *
     */
    public function __construct() {
      $this->sizes= Collection::forClass('com.flickr.FlickrPhotoSize');
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
