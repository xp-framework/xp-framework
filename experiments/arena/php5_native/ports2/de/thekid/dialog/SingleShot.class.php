<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.Date',
    'de.thekid.dialog.AlbumImage',
    'de.thekid.dialog.IEntry'
  );

  /**
   * Represents an single shot
   *
   * @see      xp://de.thekid.dialog.IEntry
   * @purpose  Value object
   */
  class SingleShot extends Object implements IEntry {
    public
      $name         = '',
      $fileName     = '',
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
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get name
     *
     * @access  public
     * @return  string
     */
    public function getName() {
      return $this->name;
    }

    /**
     * Set fileName
     *
     * @access  public
     * @param   string fileName
     */
    public function setFileName($fileName) {
      $this->fileName= $fileName;
    }

    /**
     * Get fileName
     *
     * @access  public
     * @return  string
     */
    public function getFileName() {
      return $this->fileName;
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

    /**
     * Set image
     *
     * @access  public
     * @param   &de.thekid.dialog.AlbumImage image
     */
    public function setImage(&$image) {
      $this->image= &$image;
    }

    /**
     * Get image
     *
     * @access  public
     * @return  &de.thekid.dialog.AlbumImage
     */
    public function &getImage() {
      return $this->image;
    }

  } 
?>
