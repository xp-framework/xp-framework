<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'com.google.search.custom.types';

  /**
   * Search result
   *
   * @see   http://www.google.com/cse/docs/resultsxml.html#results_xml_tag_RES
   */
  class com·google·search·custom·types·ResultSet extends Object {
    protected $total= 0;
    protected $filtered= FALSE;
    protected $entries= array();
    
    /**
     * Set estimated total number of results
     *
     * @param   int total
     */
    #[@xmlmapping(element= 'M', type= 'int')]
    public function setTotal($total) {
      $this->total= $total;
    }
    
    /**
     * Returns estimated total number
     *
     * @return  int
     */
    public function getTotal() {
      return $this->total;
    }

    /**
     * Set whether this result set was filtered
     *
     * @param   int filtered
     */
    #[@xmlmapping(element= 'FI', type= 'bool')]
    public function setFiltered($filtered) {
      $this->filtered= $filtered;
    }
    
    /**
     * Returns whether this result set was filtered
     *
     * @return  int
     */
    public function isFiltered() {
      return $this->filtered;
    }

    /**
     * Adds an entry to the resultset
     *
     * @param   com.google.search.custom.types.Result entry
     */
    #[@xmlmapping(element= 'R', class= 'com.google.search.custom.types.Result')]
    public function addEntry($entry) {
      $this->entries[]= $entry;
    }
    
    /**
     * Returns whether this result set was entries
     *
     * @return  int
     */
    public function getEntries() {
      return $this->entries;
    }
    
    /**
     * Creates a string representation of this result set
     *
     * @return  string
     */
    public function toString() {
      $entries= '';
      foreach ($this->entries as $entry) {
        $entries.= '  '.str_replace("\n", "\n  ", $entry->toString())."\n";
      }
      return sprintf(
        "%s(total ~ %d%s)@{\n%s}",
        $this->getClassName(),
        $this->total,
        $this->filtered ? ', filtered' : '',
        $entries
      );
    }
  }
?>
