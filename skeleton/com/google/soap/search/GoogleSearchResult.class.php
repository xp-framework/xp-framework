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
  class GoogleSearchResult extends Object {
    var
      $documentFiltering,
      $searchComments,
      $estimatedTotalResultsCount,
      $estimateIsExact,
      $resultElements,
      $searchQuery,
      $startIndex,
      $endIndex,
      $searchTips,
      $directoryCategories,
      $searchTime;

    /**
     * Retrieves documentFiltering
     *
     * @access  public
     * @return  bool 
     */
    function getDocumentFiltering() {
      return $this->documentFiltering;
    }

    /**
     * Sets documentFiltering
     *
     * @access  public
     * @param   bool documentFiltering
     */
    function setDocumentFiltering($documentFiltering) {
      $this->documentFiltering= $documentFiltering;
    }

    /**
     * Retrieves searchComments
     *
     * @access  public
     * @return  string 
     */
    function getSearchComments() {
      return $this->searchComments;
    }

    /**
     * Sets searchComments
     *
     * @access  public
     * @param   string searchComments
     */
    function setSearchComments($searchComments) {
      $this->searchComments= $searchComments;
    }

    /**
     * Retrieves estimatedTotalResultsCount
     *
     * @access  public
     * @return  int 
     */
    function getEstimatedTotalResultsCount() {
      return $this->estimatedTotalResultsCount;
    }

    /**
     * Sets estimatedTotalResultsCount
     *
     * @access  public
     * @param   int estimatedTotalResultsCount
     */
    function setEstimatedTotalResultsCount($estimatedTotalResultsCount) {
      $this->estimatedTotalResultsCount= $estimatedTotalResultsCount;
    }

    /**
     * Retrieves estimateIsExact
     *
     * @access  public
     * @return  bool 
     */
    function getEstimateIsExact() {
      return $this->estimateIsExact;
    }

    /**
     * Sets estimateIsExact
     *
     * @access  public
     * @param   bool estimateIsExact
     */
    function setEstimateIsExact($estimateIsExact) {
      $this->estimateIsExact= $estimateIsExact;
    }

    /**
     * Retrieves resultElements
     *
     * @access  public
     * @return  mixed (typens:ResultElementArray) 
     */
    function getResultElements() {
      return $this->resultElements;
    }

    /**
     * Sets resultElements
     *
     * @access  public
     * @param   mixed (typens:ResultElementArray) resultElements
     */
    function setResultElements($resultElements) {
      $this->resultElements= $resultElements;
    }

    /**
     * Retrieves searchQuery
     *
     * @access  public
     * @return  string 
     */
    function getSearchQuery() {
      return $this->searchQuery;
    }

    /**
     * Sets searchQuery
     *
     * @access  public
     * @param   string searchQuery
     */
    function setSearchQuery($searchQuery) {
      $this->searchQuery= $searchQuery;
    }

    /**
     * Retrieves startIndex
     *
     * @access  public
     * @return  int 
     */
    function getStartIndex() {
      return $this->startIndex;
    }

    /**
     * Sets startIndex
     *
     * @access  public
     * @param   int startIndex
     */
    function setStartIndex($startIndex) {
      $this->startIndex= $startIndex;
    }

    /**
     * Retrieves endIndex
     *
     * @access  public
     * @return  int 
     */
    function getEndIndex() {
      return $this->endIndex;
    }

    /**
     * Sets endIndex
     *
     * @access  public
     * @param   int endIndex
     */
    function setEndIndex($endIndex) {
      $this->endIndex= $endIndex;
    }

    /**
     * Retrieves searchTips
     *
     * @access  public
     * @return  string 
     */
    function getSearchTips() {
      return $this->searchTips;
    }

    /**
     * Sets searchTips
     *
     * @access  public
     * @param   string searchTips
     */
    function setSearchTips($searchTips) {
      $this->searchTips= $searchTips;
    }

    /**
     * Retrieves directoryCategories
     *
     * @access  public
     * @return  mixed (typens:DirectoryCategoryArray) 
     */
    function getDirectoryCategories() {
      return $this->directoryCategories;
    }

    /**
     * Sets directoryCategories
     *
     * @access  public
     * @param   mixed (typens:DirectoryCategoryArray) directoryCategories
     */
    function setDirectoryCategories($directoryCategories) {
      $this->directoryCategories= $directoryCategories;
    }

    /**
     * Retrieves searchTime
     *
     * @access  public
     * @return  float 
     */
    function getSearchTime() {
      return $this->searchTime;
    }

    /**
     * Sets searchTime
     *
     * @access  public
     * @param   float searchTime
     */
    function setSearchTime($searchTime) {
      $this->searchTime= $searchTime;
    }
  }
?>
