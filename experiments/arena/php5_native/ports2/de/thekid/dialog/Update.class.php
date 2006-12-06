<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Date', 'de.thekid.dialog.IEntry');

  /**
   * Represents an update to an album
   *
   * @see      xp://de.thekid.dialog.Album
   * @purpose  Value object
   */
  class Update extends Object implements IEntry {
    public
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
    public function setAlbumName($albumName) {
      $this->albumName= $albumName;
    }

    /**
     * Get albumName
     *
     * @access  public
     * @return  string
     */
    public function getAlbumName() {
      return $this->albumName;
    }

    /**
     * Set title
     *
     * @access  public
     * @param   string title
     */
    public function setTitle($title) {
      $this->title= $title;
    }

    /**
     * Get title
     *
     * @access  public
     * @return  string
     */
    public function getTitle() {
      return $this->title;
    }

    /**
     * Set description
     *
     * @access  public
     * @param   string description
     */
    public function setDescription($description) {
      $this->description= $description;
    }

    /**
     * Get description
     *
     * @access  public
     * @return  string
     */
    public function getDescription() {
      return $this->description;
    }

    /**
     * Set date
     *
     * @access  public
     * @param   &util.Date date
     */
    public function setDate(&$date) {
      $this->date= &$date;
    }

    /**
     * Get date
     *
     * @access  public
     * @return  &util.Date
     */
    public function &getDate() {
      return $this->date;
    }

  } 
?>
