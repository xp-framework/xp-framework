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
    public
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
    public function getSummary() {
      return $this->summary;
    }

    /**
     * Sets summary
     *
     * @access  public
     * @param   string summary
     */
    public function setSummary($summary) {
      $this->summary= $summary;
    }

    /**
     * Retrieves URL
     *
     * @access  public
     * @return  string 
     */
    public function getURL() {
      return $this->URL;
    }

    /**
     * Sets URL
     *
     * @access  public
     * @param   string URL
     */
    public function setURL($URL) {
      $this->URL= $URL;
    }

    /**
     * Retrieves snippet
     *
     * @access  public
     * @return  string 
     */
    public function getSnippet() {
      return $this->snippet;
    }

    /**
     * Sets snippet
     *
     * @access  public
     * @param   string snippet
     */
    public function setSnippet($snippet) {
      $this->snippet= $snippet;
    }

    /**
     * Retrieves title
     *
     * @access  public
     * @return  string 
     */
    public function getTitle() {
      return $this->title;
    }

    /**
     * Sets title
     *
     * @access  public
     * @param   string title
     */
    public function setTitle($title) {
      $this->title= $title;
    }

    /**
     * Retrieves cachedSize
     *
     * @access  public
     * @return  string 
     */
    public function getCachedSize() {
      return $this->cachedSize;
    }

    /**
     * Sets cachedSize
     *
     * @access  public
     * @param   string cachedSize
     */
    public function setCachedSize($cachedSize) {
      $this->cachedSize= $cachedSize;
    }

    /**
     * Retrieves relatedInformationPresent
     *
     * @access  public
     * @return  bool 
     */
    public function getRelatedInformationPresent() {
      return $this->relatedInformationPresent;
    }

    /**
     * Sets relatedInformationPresent
     *
     * @access  public
     * @param   bool relatedInformationPresent
     */
    public function setRelatedInformationPresent($relatedInformationPresent) {
      $this->relatedInformationPresent= $relatedInformationPresent;
    }

    /**
     * Retrieves hostName
     *
     * @access  public
     * @return  string 
     */
    public function getHostName() {
      return $this->hostName;
    }

    /**
     * Sets hostName
     *
     * @access  public
     * @param   string hostName
     */
    public function setHostName($hostName) {
      $this->hostName= $hostName;
    }

    /**
     * Retrieves directoryCategory
     *
     * @access  public
     * @return  mixed (typens:DirectoryCategory) 
     */
    public function getDirectoryCategory() {
      return $this->directoryCategory;
    }

    /**
     * Sets directoryCategory
     *
     * @access  public
     * @param   mixed directoryCategory (typens:DirectoryCategory)
     */
    public function setDirectoryCategory($directoryCategory) {
      $this->directoryCategory= $directoryCategory;
    }

    /**
     * Retrieves directoryTitle
     *
     * @access  public
     * @return  string 
     */
    public function getDirectoryTitle() {
      return $this->directoryTitle;
    }

    /**
     * Sets directoryTitle
     *
     * @access  public
     * @param   string directoryTitle
     */
    public function setDirectoryTitle($directoryTitle) {
      $this->directoryTitle= $directoryTitle;
    }
  }
?>
