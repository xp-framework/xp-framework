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
     * @param   string name
     */
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get name
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }

    /**
     * Set fileName
     *
     * @param   string fileName
     */
    public function setFileName($fileName) {
      $this->fileName= $fileName;
    }

    /**
     * Get fileName
     *
     * @return  string
     */
    public function getFileName() {
      return $this->fileName;
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

    /**
     * Set image
     *
     * @param   &de.thekid.dialog.AlbumImage image
     */
    public function setImage($image) {
      $this->image= $image;
    }

    /**
     * Get image
     *
     * @return  &de.thekid.dialog.AlbumImage
     */
    public function getImage() {
      return $this->image;
    }

    /**
     * Find an image
     *
     * @return  array
     */
    public function imageUrn($name) {
      if ($this->image->getName() === $name) return array('id' => 0);
      return NULL;
    }
  } 
?>
