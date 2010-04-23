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
  class GoogleSearchResult extends Object {
    public
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
     * @return  bool 
     */
    public function getDocumentFiltering() {
      return $this->documentFiltering;
    }

    /**
     * Sets documentFiltering
     *
     * @param   bool documentFiltering
     */
    public function setDocumentFiltering($documentFiltering) {
      $this->documentFiltering= $documentFiltering;
    }

    /**
     * Retrieves searchComments
     *
     * @return  string 
     */
    public function getSearchComments() {
      return $this->searchComments;
    }

    /**
     * Sets searchComments
     *
     * @param   string searchComments
     */
    public function setSearchComments($searchComments) {
      $this->searchComments= $searchComments;
    }

    /**
     * Retrieves estimatedTotalResultsCount
     *
     * @return  int 
     */
    public function getEstimatedTotalResultsCount() {
      return $this->estimatedTotalResultsCount;
    }

    /**
     * Sets estimatedTotalResultsCount
     *
     * @param   int estimatedTotalResultsCount
     */
    public function setEstimatedTotalResultsCount($estimatedTotalResultsCount) {
      $this->estimatedTotalResultsCount= $estimatedTotalResultsCount;
    }

    /**
     * Retrieves estimateIsExact
     *
     * @return  bool 
     */
    public function getEstimateIsExact() {
      return $this->estimateIsExact;
    }

    /**
     * Sets estimateIsExact
     *
     * @param   bool estimateIsExact
     */
    public function setEstimateIsExact($estimateIsExact) {
      $this->estimateIsExact= $estimateIsExact;
    }

    /**
     * Retrieves resultElements
     *
     * @return  com.google.soap.search.ResultElement[]
     */
    public function getResultElements() {
      return $this->resultElements;
    }

    /**
     * Retrieves number of resultElements
     *
     * @return  int
     */
    public function numResultElements() {
      return sizeof($this->resultElements);
    }

    /**
     * Retrieves whether there is at least one result element
     *
     * @return  bool
     */
    public function hasResultElements() {
      return !empty($this->resultElements);
    }

    /**
     * Retrieves resultElement at a given position. Returns NULL if the
     * position specified is out of range.
     *
     * @param   int pos
     * @return  &com.google.soap.search.ResultElement
     */
    public function getResultElement($pos) {
      if (!isset($this->resultElements[$pos])) return NULL;
      return $this->resultElements[$pos];
    }

    /**
     * Sets resultElements
     *
     * @param   mixed resultElements (typens:ResultElementArray)
     */
    public function setResultElements($resultElements) {
      $this->resultElements= $resultElements;
    }

    /**
     * Retrieves searchQuery
     *
     * @return  string 
     */
    public function getSearchQuery() {
      return $this->searchQuery;
    }

    /**
     * Sets searchQuery
     *
     * @param   string searchQuery
     */
    public function setSearchQuery($searchQuery) {
      $this->searchQuery= $searchQuery;
    }

    /**
     * Retrieves startIndex
     *
     * @return  int 
     */
    public function getStartIndex() {
      return $this->startIndex;
    }

    /**
     * Sets startIndex
     *
     * @param   int startIndex
     */
    public function setStartIndex($startIndex) {
      $this->startIndex= $startIndex;
    }

    /**
     * Retrieves endIndex
     *
     * @return  int 
     */
    public function getEndIndex() {
      return $this->endIndex;
    }

    /**
     * Sets endIndex
     *
     * @param   int endIndex
     */
    public function setEndIndex($endIndex) {
      $this->endIndex= $endIndex;
    }

    /**
     * Retrieves searchTips
     *
     * @return  string 
     */
    public function getSearchTips() {
      return $this->searchTips;
    }

    /**
     * Sets searchTips
     *
     * @param   string searchTips
     */
    public function setSearchTips($searchTips) {
      $this->searchTips= $searchTips;
    }

    /**
     * Retrieves directoryCategories
     *
     * @return  com.google.soap.search.DirectoryCategory[]
     */
    public function getDirectoryCategories() {
      return $this->directoryCategories;
    }

    /**
     * Retrieves number of directoryCategories
     *
     * @return  int
     */
    public function numDirectoryCategories() {
      return sizeof($this->directoryCategories);
    }

    /**
     * Retrieves whether there is at least one directory category
     *
     * @return  bool
     */
    public function hasDirectoryCategories() {
      return !empty($this->directoryCategories);
    }

    /**
     * Retrieves directoryCategoriy at a given position. Returns NULL if the
     * position specified is out of range.
     *
     * @param   int pos
     * @return  &com.google.soap.search.DirectoryCategory
     */
    public function getDirectoryCategory($pos) {
      if (!isset($this->directoryCategories[$pos])) return NULL;
      return $this->directoryCategories[$pos];
    }

    /**
     * Sets directoryCategories
     *
     * @param   com.google.soap.search.DirectoryCategory[] directoryCategories
     */
    public function setDirectoryCategories($directoryCategories) {
      $this->directoryCategories= $directoryCategories;
    }

    /**
     * Retrieves searchTime
     *
     * @return  float 
     */
    public function getSearchTime() {
      return $this->searchTime;
    }

    /**
     * Sets searchTime
     *
     * @param   float searchTime
     */
    public function setSearchTime($searchTime) {
      $this->searchTime= $searchTime;
    }
    
    /**
     * Create string representation
     *
     * @return  string
     */
    public function toString() {
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
