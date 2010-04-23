<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Type wrapper
   *
   * @deprecated  http://googlecode.blogspot.com/2009/08/well-earned-retirement-for-soap-search.html
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
     * @return  string 
     */
    public function getSummary() {
      return $this->summary;
    }

    /**
     * Sets summary
     *
     * @param   string summary
     */
    public function setSummary($summary) {
      $this->summary= $summary;
    }

    /**
     * Retrieves URL
     *
     * @return  string 
     */
    public function getURL() {
      return $this->URL;
    }

    /**
     * Sets URL
     *
     * @param   string URL
     */
    public function setURL($URL) {
      $this->URL= $URL;
    }

    /**
     * Retrieves snippet
     *
     * @return  string 
     */
    public function getSnippet() {
      return $this->snippet;
    }

    /**
     * Sets snippet
     *
     * @param   string snippet
     */
    public function setSnippet($snippet) {
      $this->snippet= $snippet;
    }

    /**
     * Retrieves title
     *
     * @return  string 
     */
    public function getTitle() {
      return $this->title;
    }

    /**
     * Sets title
     *
     * @param   string title
     */
    public function setTitle($title) {
      $this->title= $title;
    }

    /**
     * Retrieves cachedSize
     *
     * @return  string 
     */
    public function getCachedSize() {
      return $this->cachedSize;
    }

    /**
     * Sets cachedSize
     *
     * @param   string cachedSize
     */
    public function setCachedSize($cachedSize) {
      $this->cachedSize= $cachedSize;
    }

    /**
     * Retrieves relatedInformationPresent
     *
     * @return  bool 
     */
    public function getRelatedInformationPresent() {
      return $this->relatedInformationPresent;
    }

    /**
     * Sets relatedInformationPresent
     *
     * @param   bool relatedInformationPresent
     */
    public function setRelatedInformationPresent($relatedInformationPresent) {
      $this->relatedInformationPresent= $relatedInformationPresent;
    }

    /**
     * Retrieves hostName
     *
     * @return  string 
     */
    public function getHostName() {
      return $this->hostName;
    }

    /**
     * Sets hostName
     *
     * @param   string hostName
     */
    public function setHostName($hostName) {
      $this->hostName= $hostName;
    }

    /**
     * Retrieves directoryCategory
     *
     * @return  mixed (typens:DirectoryCategory) 
     */
    public function getDirectoryCategory() {
      return $this->directoryCategory;
    }

    /**
     * Sets directoryCategory
     *
     * @param   mixed directoryCategory (typens:DirectoryCategory)
     */
    public function setDirectoryCategory($directoryCategory) {
      $this->directoryCategory= $directoryCategory;
    }

    /**
     * Retrieves directoryTitle
     *
     * @return  string 
     */
    public function getDirectoryTitle() {
      return $this->directoryTitle;
    }

    /**
     * Sets directoryTitle
     *
     * @param   string directoryTitle
     */
    public function setDirectoryTitle($directoryTitle) {
      $this->directoryTitle= $directoryTitle;
    }
  }
?>
