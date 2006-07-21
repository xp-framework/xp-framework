<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represent a Newsgroup
   *
   * @ext      extension
   * @see      xp://peer.news.NntpConnection#getGroups
   * @purpose  Base class
   */
  class Newsgroup extends Object {
    public
      $name         = '',
      $last         = NULL,
      $first        = NULL,
      $flags        = '';

    /**
     * Constructor
     *
     * @access  private
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
     * @access  public
     * @param   string name
     */
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  string
     */
    public function getName() {
      return $this->name;
    }

    /**
     * Set Last
     *
     * @access  public
     * @param   int last
     */
    public function setLast($last) {
      $this->last= $last;
    }

    /**
     * Get Last
     *
     * @access  public
     * @return  int
     */
    public function getLast() {
      return $this->last;
    }

    /**
     * Set First
     *
     * @access  public
     * @param   int first
     */
    public function setFirst($first) {
      $this->first= $first;
    }

    /**
     * Get First
     *
     * @access  public
     * @return  int
     */
    public function getFirst() {
      return $this->first;
    }

    /**
     * Set Flags
     *
     * @access  public
     * @param   string flags
     */
    public function setFlags($flags) {
      $this->flags= $flags;
    }

    /**
     * Get Flags
     *
     * @access  public
     * @return  string
     */
    public function getFlags() {
      return $this->flags;
    }
  }
?>
