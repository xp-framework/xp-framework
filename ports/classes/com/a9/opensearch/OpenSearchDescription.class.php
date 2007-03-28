<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('com.a9.opensearch.OpenSearchUrl', 'lang.Collection');

  /**
   * XML wrapper type
   *
   * @see      http://www.opensearch.org/Specifications/OpenSearch/1.1
   * @purpose  Wrap OpenSearch XML description file
   */
  #[@xmlns(s= 'http://a9.com/-/spec/opensearch/1.1/')]
  class OpenSearchDescription extends Object {
    protected
      $shortName    = '',
      $description  = '',
      $tags         = '',
      $contact      = '',
      $urls         = array();

    /**
     * Constructor
     *
     */
    public function __construct() {
      $this->urls= Collection::forClass('com.a9.opensearch.OpenSearchUrl');
    }

    /**
     * Set shortName
     *
     * @param   string shortName
     */
    #[@xmlmapping(element= 's:ShortName')]
    public function setShortName($shortName) {
      $this->shortName= $shortName;
    }

    /**
     * Get shortName
     *
     * @return  string
     */
    #[@xmlfactory(element= 's:ShortName')]
    public function getShortName() {
      return $this->shortName;
    }

    /**
     * Set description
     *
     * @param   string description
     */
    #[@xmlmapping(element= 's:Description')]
    public function setDescription($description) {
      $this->description= $description;
    }

    /**
     * Get description
     *
     * @return  string
     */
    #[@xmlfactory(element= 's:Description')]
    public function getDescription() {
      return $this->description;
    }

    /**
     * Set tags
     *
     * @param   string tags
     */
    #[@xmlmapping(element= 's:Tags')]
    public function setTags($tags) {
      $this->tags= $tags;
    }

    /**
     * Get tags
     *
     * @return  string
     */
    #[@xmlfactory(element= 's:Tags')]
    public function getTags() {
      return $this->tags;
    }

    /**
     * Set contact
     *
     * @param   string contact
     */
    #[@xmlmapping(element= 's:Contact')]
    public function setContact($contact) {
      $this->contact= $contact;
    }

    /**
     * Get contact
     *
     * @return  string
     */
    #[@xmlfactory(element= 's:ShortName')]
    public function getContact() {
      return $this->contact;
    }

    /**
     * Add url
     *
     * @param   com.a9.opensearch.OpenSearchUrl url
     * @return  com.a9.opensearch.OpenSearchUrl the added url
     */
    #[@xmlmapping(element= 's:Url', class= 'com.a9.opensearch.OpenSearchUrl')]
    public function addUrl($url) {
      $this->urls->add($url);
      return $urk;
    }

    /**
     * Set urls
     *
     * @param   lang.Collection<com.a9.opensearch.OpenSearchUrl> urls
     */
    public function setUrls($urls) {
      $this->urls= $urls;
    }

    /**
     * Returns number of urls
     *
     * @return  int
     */
    public function numUrls() {
      return $this->urls->size();
    }

    /**
     * Returns url at a given position
     *
     * @param   int
     * @return  com.a9.opensearch.OpenSearchUrl
     */
    public function urlAt($offset) {
      return $this->urls->get($offset);
    }

    /**
     * Returns whether urls exist
     *
     * @return  int
     */
    public function hasUrls() {
      return !$this->urls->isEmpty();
    }

    /**
     * Get urls
     *
     * @return   lang.Collection<com.a9.opensearch.OpenSearchUrl> urls
     */
    #[@xmlfactory(element= 's:Url')]
    public function getUrls() {
      return $this->urls;
    }
  }
?>
