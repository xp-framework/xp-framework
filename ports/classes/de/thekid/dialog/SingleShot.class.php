<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Date', 'de.thekid.dialog.AlbumImage');

  /**
   * Represents an single shot
   *
   * @see      xp://de.thekid.dialog.IEntry
   * @purpose  Value object
   */
  class SingleShot extends Object {
    var
      $name         = '',
      $title        = '',
      $description  = '',
      $date         = NULL,
      $image        = NULL;

    /**
     * Set name
     *
     * @access  public
     * @param   string name
     */
    function setName($name) {
      $this->name= $name;
    }

    /**
     * Get name
     *
     * @access  public
     * @return  string
     */
    function getName() {
      return $this->name;
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

    /**
     * Set image
     *
     * @access  public
     * @param   &de.thekid.dialog.AlbumImage image
     */
    function setImage(&$image) {
      $this->image= &$image;
    }

    /**
     * Get image
     *
     * @access  public
     * @return  &de.thekid.dialog.AlbumImage
     */
    function &getImage() {
      return $this->image;
    }

  } implements(__FILE__, 'de.thekid.dialog.IEntry');
?>
