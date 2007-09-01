<?php
/* This class is part of the XP framework's port "Dialog"
 *
 * $Id: Album.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace de::thekid::dialog;

  ::uses(
    'util.Date',
    'de.thekid.dialog.AlbumImage',
    'de.thekid.dialog.AlbumChapter',
    'de.thekid.dialog.IEntry'
  );

  /**
   * Represents a single album.
   *
   * @purpose  Value object
   */
  class Album extends lang::Object implements IEntry {
    public
      $name         = '',
      $title        = '',
      $createdAt    = NULL,
      $description  = '',
      $highlights   = array(),
      $chapters     = array();

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
     * Add an element to highlights
     *
     * @param   &de.thekid.dialog.AlbumImage highlight
     * @return  &de.thekid.dialog.AlbumImage the added highlight
     */
    public function addHighlight($highlight) {
      $this->highlights[]= $highlight;
      return $highlight;
    }

    /**
     * Get one highlight element by position. Returns NULL if the element 
     * can not be found.
     *
     * @param   int i
     * @return  &de.thekid.dialog.AlbumImage
     */
    public function highlightAt($i) {
      if (!isset($this->highlights[$i])) return NULL;
      return $this->highlights[$i];
    }

    /**
     * Get number of highlights
     *
     * @return  int
     */
    public function numHighlights() {
      return sizeof($this->highlights);
    }

    /**
     * Add an element to chapters
     *
     * @param   &de.thekid.dialog.AlbumChapter chapter
     * @return  &de.thekid.dialog.AlbumChapter the added chapter
     */
    public function addChapter($chapter) {
      $this->chapters[]= $chapter;
      return $chapter;
    }

    /**
     * Get one chapter element by position. Returns NULL if the element 
     * can not be found.
     *
     * @param   int i
     * @return  &de.thekid.dialog.AlbumChapter
     */
    public function chapterAt($i) {
      if (!isset($this->chapters[$i])) return NULL;
      return $this->chapters[$i];
    }

    /**
     * Get number of chapters
     *
     * @return  int
     */
    public function numChapters() {
      return sizeof($this->chapters);
    }

    /**
     * Get number of images (highlights excluded)
     *
     * @return  int
     */
    public function numImages() {
      $r= 0;
      for ($i= 0, $s= sizeof($this->chapters); $i < $s; $i++) {
        $r+= $this->chapters[$i]->numImages();
      }
      return $r;
    }
    
    /**
     * Retrieve a string representation
     *
     * @return  string
     */
    public function toString() {
      $hs= '';
      for ($i= 0, $s= sizeof($this->highlights); $i < $s; $i++) {
        $hs.= '  '.$this->highlights[$i]->toString()."\n";
      }
      $cs= '';
      for ($i= 0, $s= sizeof($this->chapters); $i < $s; $i++) {
        $cs.= '  '.$this->chapters[$i]->toString()."\n";
      }
      return sprintf(
        "%s(%s)@{\n".
        "  [title        ] %s\n".
        "  [description  ] %s\n".
        "  [createdAt    ] %s\n".
        "  [highlights   ] {\n%s  }\n".
        "  [chapters     ] {\n%s  }\n".
        "}",
        $this->getClassName(),
        $this->name,
        $this->title,
        $this->description,
        ::xp::stringOf($this->createdAt),
        $hs,
        $cs
      );
    }

  } 
?>
