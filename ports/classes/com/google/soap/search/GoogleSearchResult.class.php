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
     * @return  com.google.soap.search.ResultElement[]
     */
    function getResultElements() {
      return $this->resultElements;
    }

    /**
     * Retrieves number of resultElements
     *
     * @access  public
     * @return  int
     */
    function numResultElements() {
      return sizeof($this->resultElements);
    }

    /**
     * Retrieves whether there is at least one result element
     *
     * @access  public
     * @return  bool
     */
    function hasResultElements() {
      return !empty($this->resultElements);
    }

    /**
     * Retrieves resultElement at a given position. Returns NULL if the
     * position specified is out of range.
     *
     * @access  public
     * @param   int pos
     * @return  &com.google.soap.search.ResultElement
     */
    function &getResultElement($pos) {
      if (!isset($this->resultElements[$pos])) return NULL;
      return $this->resultElements[$pos];
    }

    /**
     * Sets resultElements
     *
     * @access  public
     * @param   mixed resultElements (typens:ResultElementArray)
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
     * @return  com.google.soap.search.DirectoryCategory[]
     */
    function getDirectoryCategories() {
      return $this->directoryCategories;
    }

    /**
     * Retrieves number of directoryCategories
     *
     * @access  public
     * @return  int
     */
    function numDirectoryCategories() {
      return sizeof($this->directoryCategories);
    }

    /**
     * Retrieves whether there is at least one directory category
     *
     * @access  public
     * @return  bool
     */
    function hasDirectoryCategories() {
      return !empty($this->directoryCategories);
    }

    /**
     * Retrieves directoryCategoriy at a given position. Returns NULL if the
     * position specified is out of range.
     *
     * @access  public
     * @param   int pos
     * @return  &com.google.soap.search.DirectoryCategory
     */
    function &getDirectoryCategory($pos) {
      if (!isset($this->directoryCategories[$pos])) return NULL;
      return $this->directoryCategories[$pos];
    }

    /**
     * Sets directoryCategories
     *
     * @access  public
     * @param   com.google.soap.search.DirectoryCategory[] directoryCategories
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
    
    /**
     * Create string representation
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return sprintf(
        "%s(%d-%d/%s%.0f){\n".
        "  [searchQuery        ] %s\n".
        "  [searchTime         ] %.f seconds\n".
        "  [searchComments     ] %s\n".
        "  [searchTips         ] %s\n".
        "  [documentFiltering  ] %s\n".
        "  [directoryCategories] (%d)%s\n".
        "  [resultElements     ] (%d)%s\n".
        "}",
        $this->getClassName(),
        $this->startIndex,
        $this->endIndex,
        $this->estimateIsExact ? '' : '~',
        $this->estimatedTotalResultsCount,
        $this->searchQuery,
        $this->searchTime,
        $this->searchComments,
        $this->searchTips,
        var_export($this->documentFiltering, 1),
        $this->numDirectoryCategories(),
        $this->hasDirectoryCategories() ? var_export($this->directoryCategories, 1) : '',
        $this->numResultElements(),
        $this->hasResultElements() ? var_export($this->resultElements, 1) : ''
      );
    }
  }
?>
