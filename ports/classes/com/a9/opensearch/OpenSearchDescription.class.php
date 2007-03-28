<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'com.a9.opensearch.OpenSearchUrl', 
    'com.a9.opensearch.OpenSearchImage', 
    'lang.Collection'
  );

  /**
   * XML wrapper type
   *
   * @see      http://www.opensearch.org/Specifications/OpenSearch/1.1
   * @purpose  Wrap OpenSearch XML description file
   */
  #[@xmlns(s= 'http://a9.com/-/spec/opensearch/1.1/')]
  class OpenSearchDescription extends Object {
    protected
      $shortName    = NULL,
      $longName     = NULL,
      $description  = NULL,
      $tags         = NULL,
      $contact      = NULL,
      $image        = NULL,
      $urls         = NULL;

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
     * Set longName
     *
     * @param   string longName
     */
    #[@xmlmapping(element= 's:LongName')]
    public function setLongName($longName) {
      $this->longName= $longName;
    }

    /**
     * Get longName
     *
     * @return  string
     */
    #[@xmlfactory(element= 's:LongName')]
    public function getLongName() {
      return $this->longName;
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
     * Set Image
     *
     * @param   com.a9.opensearch.OpenSearchImage image
     */
    #[@xmlmapping(element= 's:Image', class= 'com.a9.opensearch.OpenSearchImage')]
    public function setImage($image) {
      $this->image= $image;
    }

    /**
     * Get Image
     *
     * @return  com.a9.opensearch.OpenSearchImage
     */
    #[@xmlfactory(element= 's:Image')]
    public function getImage() {
      return $this->image;
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
