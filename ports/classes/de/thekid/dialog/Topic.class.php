<?php
/* This class is part of the XP framework's port "Dialog"
 *
 * $Id$
 */

  uses(
    'util.Date',
    'de.thekid.dialog.AlbumImage',
    'de.thekid.dialog.IEntry'
  );

  /**
   * Represents a topic.
   *
   * @purpose  Value object
   */
  class Topic extends Object implements IEntry {
    public
      $name         = '',
      $title        = '',
      $createdAt    = NULL,
      $description  = '',
      $images       = array();

    /**
     * Set Name
     *
     * @param   string name
     */
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }

    /**
     * Set Title
     *
     * @param   string title
     */
    public function setTitle($title) {
      $this->title= $title;
    }

    /**
     * Get Title
     *
     * @return  string
     */
    public function getTitle() {
      return $this->title;
    }

    /**
     * Set CreatedAt
     *
     * @param   &util.Date createdAt
     */
    public function setCreatedAt($createdAt) {
      $this->createdAt= $createdAt;
    }

    /**
     * Get CreatedAt
     *
     * @return  &util.Date
     */
    public function getCreatedAt() {
      return $this->createdAt;
    }
    
    /**
     * Get date
     *
     * @see     xp://de.thekid.dialog.IEntry
     * @return  &util.Date
     */
    public function getDate() {
      return $this->createdAt;
    }

    /**
     * Set Description
     *
     * @param   string description
     */
    public function setDescription($description) {
      $this->description= $description;
    }

    /**
     * Get Description
     *
     * @return  string
     */
    public function getDescription() {
      return $this->description;
    }

    /**
     * Add an element to images
     *
     * @param   de.thekid.dialog.AlbumImage image
     * @param   string origin origin album
     */
    public function addImage(AlbumImage $image, $origin) {
      if (isset($this->images[$origin])) {    // Check for duplicates
        foreach ($this->images[$origin] as $existing) {
          if ($existing->getName() === $image->getName()) return;
        }
      } else {
        $this->images[$origin]= array();
      }
      $this->images[$origin][]= $image;
    }

    /**
     * Get featured images.
     *
     * @return  de.thekid.dialog.AlbumImage[]
     */
    public function imagesFrom($origin) {
      return $this->images[$origin];
    }

    /**
     * Get featured images.
     *
     * @return  array<string, de.thekid.dialog.AlbumImage>
     */
    public function featuredImages() {
      $r= array();
      foreach ($this->images as $key => $list) {
        $r[$key]= $list[0];
      }
      return $r;
    }

    /**
     * Get origins.
     *
     * @return  string[]
     */
    public function origins() {
      return array_keys($this->images);
    }

    /**
     * Retrieve a string representation
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        "%s(%s)@{\n".
        "  [title        ] %s\n".
        "  [description  ] %s\n".
        "  [createdAt    ] %s\n".
        "  [images       ] {\n%s  }\n".
        "}",
        $this->getClassName(),
        $this->name,
        $this->title,
        $this->description,
        xp::stringOf($this->createdAt),
        xp::stringOf($this->images)
      );
    }
  } 
?>
