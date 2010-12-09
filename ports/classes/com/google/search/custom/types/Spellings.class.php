<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'com.google.search.custom.types';

  /**
   * Spellings entry
   *
   * @see   xp://com.google.search.custom.types.Response#getSpellings
   * @see   http://www.google.com/cse/docs/resultsxml.html#results_xml_tag_Spelling
   */
  class com·google·search·custom·types·Spellings extends Object {
    protected $suggestions= array();
    
    /**
     * Adds as suggestion
     *
     * @param   string suggestion
     */
    #[@xmlmapping(element= 'Suggestion/@q')]
    public function addSuggestion($suggestion) {
      $this->suggestions[]= $suggestion;
    }
    
    /**
     * Gets all suggestions
     *
     * @return  string[]
     */
    public function getSuggestions() {
      return $this->suggestions;
    }

    /**
     * Creates a string representation of this result set
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'('.sizeof($this->suggestions).')@'.xp::stringOf($this->suggestions);
    }
  }
?>
