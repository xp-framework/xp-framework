<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'com.a9.opensearch.OpenSearchUrl', 
    'com.a9.opensearch.OpenSearchImage', 
    'com.a9.opensearch.OpenSearchQuery',
    'com.a9.opensearch.SyndicationRight', 
    'lang.Collection',
    'lang.types.ArrayList'
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
      $shortName        = NULL,
      $longName         = NULL,
      $developer        = NULL,
      $attribution      = NULL,
      $description      = NULL,
      $tags             = NULL,
      $contact          = NULL,
      $image            = NULL,
      $urls             = NULL,
      $queries          = NULL,
      $adultContent     = FALSE,
      $inputEncoding    = NULL,
      $outputEncoding   = NULL,
      $syndicationRight = SyndicationRight::IS_OPEN,
      $languages        = array('*');

    /**
     * Constructor
     *
     */
    public function __construct() {
      $this->urls= Collection::forClass('com.a9.opensearch.OpenSearchUrl');
      $this->queries= Collection::forClass('com.a9.opensearch.OpenSearchQuery');
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
     * Set developer
     *
     * @param   string developer
     */
    #[@xmlmapping(element= 's:Developer')]
    public function setDeveloper($developer) {
      $this->developer= $developer;
    }

    /**
     * Get developer
     *
     * @return  string
     */
    #[@xmlfactory(element= 's:Developer')]
    public function getDeveloper() {
      return $this->developer;
    }

    /**
     * Set attribution
     *
     * @param   string attribution
     */
    #[@xmlmapping(element= 's:Attribution')]
    public function setAttribution($attribution) {
      $this->attribution= $attribution;
    }

    /**
     * Get attribution
     *
     * @return  string
     */
    #[@xmlfactory(element= 's:Attribution')]
    public function getAttribution() {
      return $this->attribution;
    }

    /**
     * Set syndicationRight
     *
     * @see     xp://com.a9.opensearch.SyndicationRight
     * @param   string syndicationRight one of the SyndicationRight::* class constants
     */
    #[@xmlmapping(element= 's:SyndicationRight')]
    public function setSyndicationRight($syndicationRight) {
      $this->syndicationRight= strtolower($syndicationRight);   // Case-insensitive by spec
    }

    /**
     * Get syndicationRight
     *
     * @return  string
     */
    #[@xmlfactory(element= 's:SyndicationRight')]
    public function getSyndicationRight() {
      return $this->syndicationRight;
    }

    /**
     * Add language
     *
     * @param   string language
     */
    #[@xmlmapping(element= 's:Language')]
    public function addLanguage($language) {
      $this->languages[$language]= TRUE;
    }

    /**
     * Set languages
     *
     * @return  lang.types.ArrayList<string> languages
     */
    public function setLanguages(ArrayList $languages) {
      $this->languages= array_flip($languages->values);
    }

    /**
     * Get languages
     *
     * @return  lang.types.ArrayList<string>
     */
    #[@xmlfactory(element= 's:Language')]
    public function getLanguages() {
      return new ArrayList(array_values($this->languages));
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
    #[@xmlfactory(element= 's:Contact')]
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
     * Set query
     *
     * @param   com.a9.opensearch.OpenSearchQuery query
     */
    #[@xmlmapping(element= 's:Query', class= 'com.a9.opensearch.OpenSearchQuery')]
    public function addQuery($query) {
      $this->queries->add($query);
    }

    /**
     * Get query
     *
     * @return  lang.Collection<com.a9.opensearch.OpenSearchQuery>
     */
    #[@xmlfactory(element= 's:Query')]
    public function getQueries() {
      return $this->queries;
    }

    /**
     * Set queries
     *
     * @param   lang.Collection<com.a9.opensearch.OpenSearchQuery> queries
     */
    public function setQueries($queries) {
      $this->queries= $queries;
    }

    /**
     * Returns number of queries
     *
     * @return  int
     */
    public function numQueries() {
      return $this->queries->size();
    }

    /**
     * Returns url at a given position
     *
     * @param   int
     * @return  com.a9.opensearch.OpenSearchQuery
     */
    public function queryAt($offset) {
      return $this->queries->get($offset);
    }

    /**
     * Returns whether queries exist
     *
     * @return  bool
     */
    public function hasQueries() {
      return !$this->queries->isEmpty();
    }

    /**
     * Set adultContent
     *
     * @param   mixed adultContent either a string or a bool
     */
    #[@xmlmapping(element= 's:AdultContent')]
    public function setAdultContent($adultContent) {
      if (is_string($adultContent)) {
        $this->adultContent= !in_array($adultContent, array('false', '0', 'no'));
      } else {
        $this->adultContent= (bool)$adultContent;
      }
    }

    /**
     * Get adultContent
     *
     * @return  string
     */
    #[@xmlfactory(element= 's:AdultContent')]
    public function getAdultContent() {
      return $this->adultContent ? 'true' : 'false';
    }

    /**
     * Get adultContent
     *
     * @return  bool
     */
    public function isAdultContent() {
      return $this->adultContent;
    }

    /**
     * Set inputEncoding
     *
     * @param   lang.Object inputEncoding
     */
    #[@xmlmapping(element= 's:InputEncoding')]
    public function setInputEncoding($inputEncoding) {
      $this->inputEncoding= $inputEncoding;
    }

    /**
     * Get inputEncoding
     *
     * @return  lang.Object
     */
    #[@xmlfactory(element= 's:InputEncoding')]
    public function getInputEncoding() {
      return $this->inputEncoding;
    }

    /**
     * Set outputEncoding
     *
     * @param   lang.Object outputEncoding
     */
    #[@xmlmapping(element= 's:OutputEncoding')]
    public function setOutputEncoding($outputEncoding) {
      $this->outputEncoding= $outputEncoding;
    }

    /**
     * Get outputEncoding
     *
     * @return  lang.Object
     */
    #[@xmlfactory(element= 's:OutputEncoding')]
    public function getOutputEncoding() {
      return $this->outputEncoding;
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
     * @return  bool
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
