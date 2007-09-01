<?php
/* This class is part of the XP framework
 *
 * $Id: Newsgroup.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace peer::news;

  /**
   * Represent a Newsgroup
   *
   * @ext      extension
   * @see      xp://peer.news.NntpConnection#getGroups
   * @purpose  Base class
   */
  class Newsgroup extends lang::Object {
    public
      $name         = '',
      $last         = NULL,
      $first        = NULL,
      $flags        = '';

    /**
     * Constructor
     *
     * @param   string name
     * @param   string lastmessage
     * @param   string firstmessage
     */
    public function __construct($name, $last, $first, $flags) {
      $this->name= $name;
      $this->last= $last;
      $this->first= $first;
      $this->flags= $flags;
    }

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
     * Set Last
     *
     * @param   int last
     */
    public function setLast($last) {
      $this->last= $last;
    }

    /**
     * Get Last
     *
     * @return  int
     */
    public function getLast() {
      return $this->last;
    }

    /**
     * Set First
     *
     * @param   int first
     */
    public function setFirst($first) {
      $this->first= $first;
    }

    /**
     * Get First
     *
     * @return  int
     */
    public function getFirst() {
      return $this->first;
    }

    /**
     * Set Flags
     *
     * @param   string flags
     */
    public function setFlags($flags) {
      $this->flags= $flags;
    }

    /**
     * Get Flags
     *
     * @return  string
     */
    public function getFlags() {
      return $this->flags;
    }
  }
?>
