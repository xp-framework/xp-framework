<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Query object
   *
   * @see   xp://com.google.search.custom.GoogleSearchClient
   */
  class GoogleSearchQuery extends Object {
    protected $term= NULL;
    protected $start= 0;
    
    /**
     * Constructor
     *
     * @param   string term
     */
    public function __construct($term= NULL) {
      $this->term= $term;
    }
    
    /**
     * Sets term
     *
     * @param   string term
     * @return  com.google.search.custom.GoogleSearchQuery this
     */
    public function withTerm($term) {
      $this->term= $term;
      return $this;
    }
    
    /**
     * Gets term
     *
     * @return  string
     */
    public function getTerm() {
      return $this->term;
    }

    /**
     * Sets where to start at
     *
     * @param   int start
     * @return  com.google.search.custom.GoogleSearchQuery this
     */
    public function startingAt($start) {
      $this->start= $start;
      return $this;
    }
    
    /**
     * Gets where to start at
     *
     * @return  int
     */
    public function getStart() {
      return $this->start;
    }
  }
?>
