<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  // Header identifier
  define('CHM_HEADER_IDENTIFIER',   'ITSF');

  /**
   * CHM header
   *
   * @see      xp://com.microsoft.format.chm.CHMFile#getHeader
   * @purpose  CHM file class
   * @experimental
   */
  class CHMHeader extends Object {
    var
      $identifier       = CHM_HEADER_IDENTIFIER,
      $version          = 0,
      $length           = 0,
      $unknown          = 0,
      $time             = 0,
      $lang             = 0,
      $guid1            = '',
      $guid2            = '',
      $section          = array(),
      $content_offset   = 0;

    /**
     * Set Identifier
     *
     * @access  public
     * @param   string identifier
     */
    function setIdentifier($identifier) {
      $this->identifier= $identifier;
    }

    /**
     * Get Identifier
     *
     * @access  public
     * @return  string
     */
    function getIdentifier() {
      return $this->identifier;
    }

    /**
     * Set Version
     *
     * @access  public
     * @param   int version
     */
    function setVersion($version) {
      $this->version= $version;
    }

    /**
     * Get Version
     *
     * @access  public
     * @return  int
     */
    function getVersion() {
      return $this->version;
    }

    /**
     * Set Length
     *
     * @access  public
     * @param   int length
     */
    function setLength($length) {
      $this->length= $length;
    }

    /**
     * Get Length
     *
     * @access  public
     * @return  int
     */
    function getLength() {
      return $this->length;
    }

    /**
     * Set Time
     *
     * @access  public
     * @param   int time
     */
    function setTime($time) {
      $this->time= $time;
    }

    /**
     * Get Time
     *
     * @access  public
     * @return  int
     */
    function getTime() {
      return $this->time;
    }

    /**
     * Set Lang
     *
     * @access  public
     * @param   int lang
     */
    function setLang($lang) {
      $this->lang= $lang;
    }

    /**
     * Get Lang
     *
     * @access  public
     * @return  int
     */
    function getLang() {
      return $this->lang;
    }

    /**
     * Set Guid1
     *
     * @access  public
     * @param   string guid1
     */
    function setGuid1($guid1) {
      $this->guid1= $guid1;
    }

    /**
     * Get Guid1
     *
     * @access  public
     * @return  string
     */
    function getGuid1() {
      return $this->guid1;
    }

    /**
     * Set Guid2
     *
     * @access  public
     * @param   string guid2
     */
    function setGuid2($guid2) {
      $this->guid2= $guid2;
    }

    /**
     * Get Guid2
     *
     * @access  public
     * @return  string
     */
    function getGuid2() {
      return $this->guid2;
    }

    /**
     * Set Section
     *
     * @access  public
     * @param   mixed[] section
     */
    function setSection($section) {
      $this->section= $section;
    }

    /**
     * Get Section
     *
     * @access  public
     * @return  mixed[]
     */
    function getSection() {
      return $this->section;
    }

    /**
     * Set Content_offset
     *
     * @access  public
     * @param   int content_offset
     */
    function setContent_offset($content_offset) {
      $this->content_offset= $content_offset;
    }

    /**
     * Get Content_offset
     *
     * @access  public
     * @return  int
     */
    function getContent_offset() {
      return $this->content_offset;
    }
  }
?>
