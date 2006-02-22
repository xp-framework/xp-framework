<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.Collection',
    'com.flickr.FlickrPhoto'
  );

  /**
   * Collection of Flickr photos
   *
   * @purpose  Collection of photos
   */
  class FlickrPhotoCollection extends Object {
    var
      $photos   = NULL,
      $page     = 1,
      $pages    = 1,
      $perPage  = 1,
      $total    = 1;


    /**
     * Constructor
     *
     * @access  public
     */
    function __construct() {
      $this->photos= &Collection::forClass('com.flickr.FlickrPhoto');
    }
    
    /**
     * Set Photos
     *
     * @access  public
     * @param   &lang.Object photos
     */
    #[@xmlmapping(element= 'photo', class= 'com.flickr.FlickrPhoto')]
    function addPhoto(&$photo) {
      $this->photos->add($photo);
      return $photo;
    }

    /**
     * Get Photos
     *
     * @access  public
     * @return  &lang.Object
     */
    function &getPhotos() {
      return $this->photos;
    }

    /**
     * Set Page
     *
     * @access  public
     * @param   mixed page
     */
    #[@xmlmapping(element= '@page')]
    function setPage($page) {
      $this->page= $page;
    }

    /**
     * Get Page
     *
     * @access  public
     * @return  mixed
     */
    function getPage() {
      return $this->page;
    }

    /**
     * Set Pages
     *
     * @access  public
     * @param   mixed pages
     */
    #[@xmlmapping(element= '@pages')]
    function setPages($pages) {
      $this->pages= $pages;
    }

    /**
     * Get Pages
     *
     * @access  public
     * @return  mixed
     */
    function getPages() {
      return $this->pages;
    }

    /**
     * Set PerPage
     *
     * @access  public
     * @param   mixed perPage
     */
    #[@xmlmapping(element= '@perpage')]
    function setPerPage($perPage) {
      $this->perPage= $perPage;
    }

    /**
     * Get PerPage
     *
     * @access  public
     * @return  mixed
     */
    function getPerPage() {
      return $this->perPage;
    }

    /**
     * Set Total
     *
     * @access  public
     * @param   mixed total
     */
    #[@xmlmapping(element= '@total')]
    function setTotal($total) {
      $this->total= $total;
    }

    /**
     * Get Total
     *
     * @access  public
     * @return  mixed
     */
    function getTotal() {
      return $this->total;
    }
  }
?>
