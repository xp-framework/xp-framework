<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  uses('webservices.soap.SoapDriver');
  
  /**
   * WSDL description of the Google Web APIs.
   *
   * The Google Web APIs are in beta release. All interfaces are subject to
   * change as we refine and extend our APIs. Please see the terms of use
   * for more information.
   *
   * Example:
   * <code>
   *   uses('com.google.soap.search.GoogleSearchClient');
   *
   *   $g= new GoogleSearchClient();
   *   $r= $g->doGoogleSearch(
   *     $license_key,
   *     $query,
   *     0,               // start
   *     10,              // maxResults
   *     FALSE,           // filter
   *     '',              // restrict
   *     FALSE,           // safeSearch
   *     '',              // lr
   *     '',              // ie
   *     ''               // oe
   *   );
   *   
   *   echo $r->toString();
   * </code>
   *
   * Note: You need a valid license key to run the search. 
   * 
   * @deprecated  http://googlecode.blogspot.com/2009/08/well-earned-retirement-for-soap-search.html
   * @see      http://www.google.com/apis/api_faq.html#tech5 Why do I need a license key?
   * @see      http://api.google.com/GoogleSearch.wsdl The WSDL this was generated from
   * @see      http://www.google.com/apis/reference.html API reference
   * @purpose  Google SOAP service wrapper class
   */  
  class GoogleSearchClient extends Object {
    public
      $client = NULL;
      
    /**
     * Constructor
     *
     * @param   string endpoint default 'http://api.google.com/search/beta2'
     */
    public function __construct($endpoint= 'http://api.google.com/search/beta2') {
      $this->client= SoapDriver::getInstance()->forEndpoint($endpoint, 'urn:GoogleSearch');

      $this->client->registerMapping(
        new QName('urn:GoogleSearch', 'GoogleSearchResult'), 
        XPClass::forName('com.google.soap.search.GoogleSearchResult')
      );
      $this->client->registerMapping(
        new QName('urn:GoogleSearch', 'ResultElement'), 
        XPClass::forName('com.google.soap.search.ResultElement')
      );
      $this->client->registerMapping(
        new QName('urn:GoogleSearch', 'DirectoryCategory'), 
        XPClass::forName('com.google.soap.search.DirectoryCategory')
      );
    }

    /**
     * Invokes the method "doGetCachedPage"
     *
     * @param   string key
     * @param   string url
     * @return  webservices.soap.types.SOAPBase64Binary
     * @throws  webservices.soap.SOAPFaultException in case a fault occurs
     * @throws  io.IOException in case an I/O error occurs
     * @throws  xml.XMLFormatException in case not-well-formed XML is returned
     */
    public function doGetCachedPage($key, $url) {
      return $this->client->invoke(
        'doGetCachedPage',
        new Parameter('key', $key),
        new Parameter('url', $url)
      );
    }

    /**
     * Invokes the method "doSpellingSuggestion"
     *
     * @param   string key
     * @param   string phrase
     * @return  string
     * @throws  webservices.soap.SOAPFaultException in case a fault occurs
     * @throws  io.IOException in case an I/O error occurs
     * @throws  xml.XMLFormatException in case not-well-formed XML is returned
     */
    public function doSpellingSuggestion($key, $phrase) {
      return $this->client->invoke(
        'doSpellingSuggestion',
        new Parameter('key', $key),
        new Parameter('phrase', $phrase)
      );
    }

    /**
     * Invokes the method "doGoogleSearch"
     *
     * @param   string key
     * @param   string q
     * @param   int start
     * @param   int maxResults
     * @param   bool filter
     * @param   string restrict
     * @param   bool safeSearch
     * @param   string lr
     * @param   string ie
     * @param   string oe
     * @return  com.google.soap.search.GoogleSearchResult
     * @throws  webservices.soap.SOAPFaultException in case a fault occurs
     * @throws  io.IOException in case an I/O error occurs
     * @throws  xml.XMLFormatException in case not-well-formed XML is returned
     * @see     http://www.google.com/apis/reference.html#searchrequest Search Parameters 
     */
    public function doGoogleSearch($key, $q, $start, $maxResults, $filter, $restrict, $safeSearch, $lr, $ie, $oe) {
      return $this->client->invoke(
        'doGoogleSearch',
        new Parameter('key', $key),
        new Parameter('q', $q),
        new Parameter('start', $start),
        new Parameter('maxResults', $maxResults),
        new Parameter('filter', $filter),
        new Parameter('restrict', $restrict),
        new Parameter('safeSearch', $safeSearch),
        new Parameter('lr', $lr),
        new Parameter('ie', $ie),
        new Parameter('oe', $oe)
      );
    }
  }
?>
