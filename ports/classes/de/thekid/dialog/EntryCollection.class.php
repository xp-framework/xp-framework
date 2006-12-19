<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('de.thekid.dialog.IEntry');

  /**
   * Represents a collection of any IEntry objects
   *
   * @see      xp://de.thekid.dialog.IEntry
   * @purpose  Value object
   */
  class EntryCollection extends Object implements IEntry {
    public
      $name         = '',
      $title        = '',
      $createdAt    = NULL,
      $description  = '',
      $entries      = array();

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
     * Set createdAt
     *
     * @access  public
     * @param   &lang.Object createdAt
     */
    public function setCreatedAt(&$createdAt) {
      $this->createdAt= &$createdAt;
    }

    /**
     * Get createdAt
     *
     * @access  public
     * @return  &lang.Object
     */
    public function &getCreatedAt() {
      return $this->createdAt;
    }

    /**
     * Get date
     *
     * @see     xp://de.thekid.dialog.IEntry
     * @access  public
     * @return  &util.Date
     */
    public function &getDate() {
      return $this->createdAt;
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
     * Add an element to entries
     *
     * @access  public
     * @param   &de.thekid.dialog.IEntry entry
     * @return  &de.thekid.dialog.IEntry the added entry
     */
    public function &addEntry(&$entry) {
      $this->entries[]= &$entry;
      return $entry;
    }

    /**
     * Get one entry element by position. Returns NULL if the element 
     * can not be found.
     *
     * @access  public
     * @param   int i
     * @return  &de.thekid.dialog.IEntry
     */
    public function &entryAt($i) {
      if (!isset($this->entries[$i])) return NULL;
      return $this->entries[$i];
    }

    /**
     * Get number of entries
     *
     * @access  public
     * @return  int
     */
    public function numEntries() {
      return sizeof($this->entries);
    }
  
  } 
?>
