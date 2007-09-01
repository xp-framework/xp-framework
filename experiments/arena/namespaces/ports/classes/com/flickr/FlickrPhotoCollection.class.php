<?php
/* This class is part of the XP framework
 *
 * $Id: FlickrPhotoCollection.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace com::flickr;

  ::uses(
    'lang.Collection',
    'com.flickr.FlickrPhoto'
  );

  /**
   * Collection of Flickr photos
   *
   * @purpose  Collection of photos
   */
  class FlickrPhotoCollection extends lang::Object {
    public
      $photos   = NULL,
      $page     = 1,
      $pages    = 1,
      $perPage  = 1,
      $total    = 1;
    
    public
      $_client   = NULL;

    /**
     * Set Client
     *
     * @param   &com.flickr.xmlrpc.FlickrClient client
     */
    public function setClient($client) {
      $this->_client= $client;
      for ($i= 0; $i < $this->photos->size(); $i++) {
        $p= $this->photos->get($i);
        $p->setClient($client);
      }
    }

    /**
     * Constructor
     *
     */
    public function __construct() {
      $this->photos= lang::Collection::forClass('com.flickr.FlickrPhoto');
    }
    
    /**
     * Set Photos
     *
     * @param   &lang.Object photos
     */
    #[@xmlmapping(element= 'photo', class= 'com.flickr.FlickrPhoto')]
    public function addPhoto($photo) {
      $this->photos->add($photo);
      return $photo;
    }

    /**
     * Get Photos
     *
     * @return  &lang.Object
     */
    public function getPhotos() {
      return $this->photos;
    }

    /**
     * Set Page
     *
     * @param   mixed page
     */
    #[@xmlmapping(element= '@page')]
    public function setPage($page) {
      $this->page= $page;
    }

    /**
     * Get Page
     *
     * @return  mixed
     */
    public function getPage() {
      return $this->page;
    }

    /**
     * Set Pages
     *
     * @param   mixed pages
     */
    #[@xmlmapping(element= '@pages')]
    public function setPages($pages) {
      $this->pages= $pages;
    }

    /**
     * Get Pages
     *
     * @return  mixed
     */
    public function getPages() {
      return $this->pages;
    }

    /**
     * Set PerPage
     *
     * @param   mixed perPage
     */
    #[@xmlmapping(element= '@perpage')]
    public function setPerPage($perPage) {
      $this->perPage= $perPage;
    }

    /**
     * Get PerPage
     *
     * @return  mixed
     */
    public function getPerPage() {
      return $this->perPage;
    }

    /**
     * Set Total
     *
     * @param   mixed total
     */
    #[@xmlmapping(element= '@total')]
    public function setTotal($total) {
      $this->total= $total;
    }

    /**
     * Get Total
     *
     * @return  mixed
     */
    public function getTotal() {
      return $this->total;
    }
  }
?>
