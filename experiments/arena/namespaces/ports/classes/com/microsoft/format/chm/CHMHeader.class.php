<?php
/* This class is part of the XP framework
 *
 * $Id: CHMHeader.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace com::microsoft::format::chm;

  // Header identifier
  define('CHM_HEADER_IDENTIFIER',   'ITSF');

  /**
   * CHM header
   *
   * @see      xp://com.microsoft.format.chm.CHMFile#getHeader
   * @purpose  CHM file class
   * @experimental
   */
  class CHMHeader extends lang::Object {
    public
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
     * @param   string identifier
     */
    public function setIdentifier($identifier) {
      $this->identifier= $identifier;
    }

    /**
     * Get Identifier
     *
     * @return  string
     */
    public function getIdentifier() {
      return $this->identifier;
    }

    /**
     * Set Version
     *
     * @param   int version
     */
    public function setVersion($version) {
      $this->version= $version;
    }

    /**
     * Get Version
     *
     * @return  int
     */
    public function getVersion() {
      return $this->version;
    }

    /**
     * Set Length
     *
     * @param   int length
     */
    public function setLength($length) {
      $this->length= $length;
    }

    /**
     * Get Length
     *
     * @return  int
     */
    public function getLength() {
      return $this->length;
    }

    /**
     * Set Time
     *
     * @param   int time
     */
    public function setTime($time) {
      $this->time= $time;
    }

    /**
     * Get Time
     *
     * @return  int
     */
    public function getTime() {
      return $this->time;
    }

    /**
     * Set Lang
     *
     * @param   int lang
     */
    public function setLang($lang) {
      $this->lang= $lang;
    }

    /**
     * Get Lang
     *
     * @return  int
     */
    public function getLang() {
      return $this->lang;
    }

    /**
     * Set Guid1
     *
     * @param   string guid1
     */
    public function setGuid1($guid1) {
      $this->guid1= $guid1;
    }

    /**
     * Get Guid1
     *
     * @return  string
     */
    public function getGuid1() {
      return $this->guid1;
    }

    /**
     * Set Guid2
     *
     * @param   string guid2
     */
    public function setGuid2($guid2) {
      $this->guid2= $guid2;
    }

    /**
     * Get Guid2
     *
     * @return  string
     */
    public function getGuid2() {
      return $this->guid2;
    }

    /**
     * Set Section
     *
     * @param   mixed[] section
     */
    public function setSection($section) {
      $this->section= $section;
    }

    /**
     * Get Section
     *
     * @return  mixed[]
     */
    public function getSection() {
      return $this->section;
    }

    /**
     * Set Content_offset
     *
     * @param   int content_offset
     */
    public function setContent_offset($content_offset) {
      $this->content_offset= $content_offset;
    }

    /**
     * Get Content_offset
     *
     * @return  int
     */
    public function getContent_offset() {
      return $this->content_offset;
    }
  }
?>
