<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Wraps a complete htdig search result. This includes the 
   * search entries, as well as the search metadata.
   *
   * @purpose  Wrap search result
   */
  class HtdigResultset extends Object {
    var
      $metaresult=  NULL,
      $results=     array();
    
    var
      $_csvdef=     NULL;

    /**
     * Set Csvdef.
     *
     * @access  public
     * @param   &array _cvsdef
     */
    function setCsvdef($csvdef) {
      $this->_csvdef= $csvdef;
    }

    /**
     * Set Metaresult
     *
     * @access  public
     * @param   &mixed metaresult
     */
    function setMetaresult(&$metaresult) {
      $this->metaresult= &$metaresult;
    }

    /**
     * Get Metaresult
     *
     * @access  public
     * @return  &lang.Object
     */
    function &getMetaresult() {
      return $this->metaresult;
    }

    /**
     * Set Results
     *
     * @access  public
     * @param   mixed[] results
     */
    function setResults($results) {
      $this->results= $results;
    }

    /**
     * Get Results
     *
     * @access  public
     * @return  mixed[]
     */
    function getResults() {
      return $this->results;
    }
    
    /**
     * Adds an entry to the result entries.
     *
     * @access  public
     * @param   &mixed array raw form of result data
     */
    function addResult($result) {
      $res= array();
      foreach (array_keys($result) as $idx) {
        $res[$this->_csvdef[$idx]]= &$result[$idx];
      }
      
      if ($entry= &HtdigEntry::fromArray($res))
        $this->results[]= &$entry;
    }
  }
?>
