<?php
/* This class is part of the XP framework
 *
 * $Id: EntryCollection.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace de::thekid::dialog;

  ::uses('de.thekid.dialog.IEntry');

  /**
   * Represents a collection of any IEntry objects
   *
   * @see      xp://de.thekid.dialog.IEntry
   * @purpose  Value object
   */
  class EntryCollection extends lang::Object implements IEntry {
    public
      $name         = '',
      $title        = '',
      $createdAt    = NULL,
      $description  = '',
      $entries      = array();

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
     * Set createdAt
     *
     * @param   &lang.Object createdAt
     */
    public function setCreatedAt($createdAt) {
      $this->createdAt= $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return  &lang.Object
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
     * Add an element to entries
     *
     * @param   &de.thekid.dialog.IEntry entry
     * @return  &de.thekid.dialog.IEntry the added entry
     */
    public function addEntry($entry) {
      $this->entries[]= $entry;
      return $entry;
    }

    /**
     * Get one entry element by position. Returns NULL if the element 
     * can not be found.
     *
     * @param   int i
     * @return  &de.thekid.dialog.IEntry
     */
    public function entryAt($i) {
      if (!isset($this->entries[$i])) return NULL;
      return $this->entries[$i];
    }

    /**
     * Get number of entries
     *
     * @return  int
     */
    public function numEntries() {
      return sizeof($this->entries);
    }
  
  } 
?>
