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
    var
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
    function __construct($name, $last, $first, $flags) {
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
    function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  string
     */
    function getName() {
      return $this->name;
    }

    /**
     * Set Last
     *
     * @access  public
     * @param   int last
     */
    function setLast($last) {
      $this->last= $last;
    }

    /**
     * Get Last
     *
     * @access  public
     * @return  int
     */
    function getLast() {
      return $this->last;
    }

    /**
     * Set First
     *
     * @access  public
     * @param   int first
     */
    function setFirst($first) {
      $this->first= $first;
    }

    /**
     * Get First
     *
     * @access  public
     * @return  int
     */
    function getFirst() {
      return $this->first;
    }

    /**
     * Set Flags
     *
     * @access  public
     * @param   string flags
     */
    function setFlags($flags) {
      $this->flags= $flags;
    }

    /**
     * Get Flags
     *
     * @access  public
     * @return  string
     */
    function getFlags() {
      return $this->flags;
    }
  }
?>
