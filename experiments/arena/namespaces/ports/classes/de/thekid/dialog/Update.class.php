<?php
/* This class is part of the XP framework
 *
 * $Id: Update.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace de::thekid::dialog;

  ::uses('util.Date', 'de.thekid.dialog.IEntry');

  /**
   * Represents an update to an album
   *
   * @see      xp://de.thekid.dialog.Album
   * @purpose  Value object
   */
  class Update extends lang::Object implements IEntry {
    public
      $albumName    = '',
      $title        = '',
      $description  = '',
      $date         = NULL;

    /**
     * Set albumName
     *
     * @param   string albumName
     */
    public function setAlbumName($albumName) {
      $this->albumName= $albumName;
    }

    /**
     * Get albumName
     *
     * @return  string
     */
    public function getAlbumName() {
      return $this->albumName;
    }

    /**
     * Set title
     *
     * @param   string title
     */
    public function setTitle($title) {
      $this->title= $title;
    }

    /**
     * Get title
     *
     * @return  string
     */
    public function getTitle() {
      return $this->title;
    }

    /**
     * Set description
     *
     * @param   string description
     */
    public function setDescription($description) {
      $this->description= $description;
    }

    /**
     * Get description
     *
     * @return  string
     */
    public function getDescription() {
      return $this->description;
    }

    /**
     * Set date
     *
     * @param   &util.Date date
     */
    public function setDate($date) {
      $this->date= $date;
    }

    /**
     * Get date
     *
     * @return  &util.Date
     */
    public function getDate() {
      return $this->date;
    }

  } 
?>
