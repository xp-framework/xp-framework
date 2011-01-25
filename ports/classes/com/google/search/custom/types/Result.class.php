<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'com.google.search.custom.types';

  /**
   * Search result entry
   *
   * @see   xp://com.google.search.custom.types.ResultSet#getEntries
   * @see   http://www.google.com/cse/docs/resultsxml.html#results_xml_tag_R
   */
  class com·google·search·custom·types·Result extends Object {
    protected $index= 0;
    protected $url= '';
    protected $title= '';
    protected $language= NULL;
    protected $mimeType= NULL;
    protected $excerpt= '';
    protected $details= array();
    
    /**
     * Sets index
     *
     * @param   int index
     */
    #[@xmlmapping(element= '@N', type= 'int')]
    public function setIndex($index) {
      $this->index= $index;
    }
    
    /**
     * Returns index
     *
     * @return  string
     */
    public function getIndex() {
      return $this->index;
    }

    /**
     * Sets url
     *
     * @param   string url
     */
    #[@xmlmapping(element= 'U')]
    public function setUrl($url) {
      $this->url= $url;
    }
    
    /**
     * Returns url
     *
     * @return  string
     */
    public function getUrl() {
      return $this->url;
    }

    /**
     * Sets title
     *
     * @param   string title
     */
    #[@xmlmapping(element= 'T')]
    public function setTitle($title) {
      $this->title= $title;
    }
    
    /**
     * Returns title
     *
     * @return  string
     */
    public function getTitle() {
      return $this->title;
    }

    /**
     * Sets language
     *
     * @param   string language
     */
    #[@xmlmapping(element= 'LANG')]
    public function setLanguage($language) {
      $this->language= $language;
    }
    
    /**
     * Returns language
     *
     * @return  string
     */
    public function getLanguage() {
      return $this->language;
    }

    /**
     * Sets mimeType
     *
     * @param   string mimeType
     */
    #[@xmlmapping(element= 'MIME')]
    public function setMimeType($mimeType) {
      $this->mimeType= $mimeType;
    }
    
    /**
     * Returns mimeType
     *
     * @return  string
     */
    public function getMimeType() {
      return $this->mimeType;
    }

    /**
     * Sets excerpt
     *
     * @param   string excerpt
     */
    #[@xmlmapping(element= 'S')]
    public function setExcerpt($excerpt) {
      $this->excerpt= $excerpt;
    }
    
    /**
     * Returns excerpt
     *
     * @return  string
     */
    public function getExcerpt() {
      return $this->excerpt;
    }

    /**
     * Sets a detail info
     *
     * @param   string name
     * @param   string value
     */
    #[@xmlmapping(element= 'FS', pass= array('@NAME', '@VALUE'))]
    public function setDetail($name, $value) {
      $this->details[$name]= $value;
    }
    
    /**
     * Returns details
     *
     * @return  string
     */
    public function getDetails() {
      return $this->details;
    }
    
    /**
     * Creates a string representation of this result set entry
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        "%s(#%d)@{\n".
        "  [title]    %s\n".
        "  [url]      %s\n".
        "  [language] %s\n".
        "  [mimetype] %s\n".
        "  [excerpt]  %s\n".
        "  [details]  %s\n".
        "}",
        $this->getClassName(),
        $this->index,
        $this->title,
        $this->url,
        $this->language ? $this->language : '(none)',
        $this->mimeType ? $this->mimeType : '(none)',
        $this->excerpt,
        xp::stringOf($this->details)
      );
    }
  }
?>
