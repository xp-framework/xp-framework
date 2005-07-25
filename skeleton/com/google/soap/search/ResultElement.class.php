<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Type wrapper
   *
   * @purpose  Specialized SOAP type
   */
  class ResultElement extends Object {
    var
      $summary,
      $URL,
      $snippet,
      $title,
      $cachedSize,
      $relatedInformationPresent,
      $hostName,
      $directoryCategory,
      $directoryTitle;

    /**
     * Retrieves summary
     *
     * @access  public
     * @return  string 
     */
    function getSummary() {
      return $this->summary;
    }

    /**
     * Sets summary
     *
     * @access  public
     * @param   string summary
     */
    function setSummary($summary) {
      $this->summary= $summary;
    }

    /**
     * Retrieves URL
     *
     * @access  public
     * @return  string 
     */
    function getURL() {
      return $this->URL;
    }

    /**
     * Sets URL
     *
     * @access  public
     * @param   string URL
     */
    function setURL($URL) {
      $this->URL= $URL;
    }

    /**
     * Retrieves snippet
     *
     * @access  public
     * @return  string 
     */
    function getSnippet() {
      return $this->snippet;
    }

    /**
     * Sets snippet
     *
     * @access  public
     * @param   string snippet
     */
    function setSnippet($snippet) {
      $this->snippet= $snippet;
    }

    /**
     * Retrieves title
     *
     * @access  public
     * @return  string 
     */
    function getTitle() {
      return $this->title;
    }

    /**
     * Sets title
     *
     * @access  public
     * @param   string title
     */
    function setTitle($title) {
      $this->title= $title;
    }

    /**
     * Retrieves cachedSize
     *
     * @access  public
     * @return  string 
     */
    function getCachedSize() {
      return $this->cachedSize;
    }

    /**
     * Sets cachedSize
     *
     * @access  public
     * @param   string cachedSize
     */
    function setCachedSize($cachedSize) {
      $this->cachedSize= $cachedSize;
    }

    /**
     * Retrieves relatedInformationPresent
     *
     * @access  public
     * @return  bool 
     */
    function getRelatedInformationPresent() {
      return $this->relatedInformationPresent;
    }

    /**
     * Sets relatedInformationPresent
     *
     * @access  public
     * @param   bool relatedInformationPresent
     */
    function setRelatedInformationPresent($relatedInformationPresent) {
      $this->relatedInformationPresent= $relatedInformationPresent;
    }

    /**
     * Retrieves hostName
     *
     * @access  public
     * @return  string 
     */
    function getHostName() {
      return $this->hostName;
    }

    /**
     * Sets hostName
     *
     * @access  public
     * @param   string hostName
     */
    function setHostName($hostName) {
      $this->hostName= $hostName;
    }

    /**
     * Retrieves directoryCategory
     *
     * @access  public
     * @return  mixed (typens:DirectoryCategory) 
     */
    function getDirectoryCategory() {
      return $this->directoryCategory;
    }

    /**
     * Sets directoryCategory
     *
     * @access  public
     * @param   mixed directoryCategory (typens:DirectoryCategory)
     */
    function setDirectoryCategory($directoryCategory) {
      $this->directoryCategory= $directoryCategory;
    }

    /**
     * Retrieves directoryTitle
     *
     * @access  public
     * @return  string 
     */
    function getDirectoryTitle() {
      return $this->directoryTitle;
    }

    /**
     * Sets directoryTitle
     *
     * @access  public
     * @param   string directoryTitle
     */
    function setDirectoryTitle($directoryTitle) {
      $this->directoryTitle= $directoryTitle;
    }
  }
?>
