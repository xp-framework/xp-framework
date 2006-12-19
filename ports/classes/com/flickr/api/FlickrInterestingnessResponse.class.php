<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.Collection',
    'com.flickr.FlickrPhotoCollection'
  );

  /**
   * Response to Interestingness query
   *
   * @purpose  Value object
   */
  class FlickrInterestingnessResponse extends Object {
    public
      $photos   =  NULL;
    
    /**
     * Constructor
     *
     * @access  public
     */
    public function __construct() {
      $this->photos= &Collection::forClass('com.flickr.FlickrPhoto');
    }
    
    /**
     * Add a photo
     *
     * @access  public
     * @param   &com.flickr.FlickrPhoto photo
     */
    #[@xmlmapping(element= 'photo', class= 'com.flickr.FlickrPhoto')]
    public function addPhoto(&$photo) {
      $this->photos->add($photo);
    }
  }
?>
