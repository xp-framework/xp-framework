<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Date');

  /**
   * Represents an update to an album
   *
   * @see      xp://de.thekid.dialog.Album
   * @purpose  Value object
   */
  class Update extends Object {
    var
      $albumName    = '',
      $title        = '',
      $description  = '',
      $date         = NULL;

    /**
     * Set albumName
     *
     * @access  public
     * @param   string albumName
     */
    function setAlbumName($albumName) {
      $this->albumName= $albumName;
    }

    /**
     * Get albumName
     *
     * @access  public
     * @return  string
     */
    function getAlbumName() {
      return $this->albumName;
    }

    /**
     * Set title
     *
     * @access  public
     * @param   string title
     */
    function setTitle($title) {
      $this->title= $title;
    }

    /**
     * Get title
     *
     * @access  public
     * @return  string
     */
    function getTitle() {
      return $this->title;
    }

    /**
     * Set description
     *
     * @access  public
     * @param   string description
     */
    function setDescription($description) {
      $this->description= $description;
    }

    /**
     * Get description
     *
     * @access  public
     * @return  string
     */
    function getDescription() {
      return $this->description;
    }

    /**
     * Set date
     *
     * @access  public
     * @param   &util.Date date
     */
    function setDate(&$date) {
      $this->date= &$date;
    }

    /**
     * Get date
     *
     * @access  public
     * @return  &util.Date
     */
    function &getDate() {
      return $this->date;
    }
  }
?>
