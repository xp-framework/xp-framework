<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'xml.soap.SOAPClient',
    'xml.soap.transport.SOAPHTTPTransport',
    'xml.soap.types.SOAPNamedItem'
  );

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
   *   try(); {
   *     $r= $g->doGoogleSearch(
   *       $license_key,
   *       $query,
   *       0,               // start
   *       10,              // maxResults
   *       FALSE,           // filter
   *       '',              // restrict
   *       FALSE,           // safeSearch
   *       '',              // lr
   *       '',              // ie
   *       ''               // oe
   *     );
   *   } if (catch('Exception', $e)) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   *   
   *   echo $r->toString();
   * </code>
   *
   * Note: You need a valid license key to run the search. 
   * 
   * @see      http://www.google.com/apis/api_faq.html#tech5 Why do I need a license key?
   * @see      http://api.google.com/GoogleSearch.wsdl The WSDL this was generated from
   * @see      http://www.google.com/apis/reference.html API reference
   * @purpose  Google SOAP service wrapper class
   */  
  class GoogleSearchClient extends SOAPClient {
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string endpoint default 'http://api.google.com/search/beta2'
     */
    public function __construct($endpoint= 'http://api.google.com/search/beta2') {
      parent::__construct(
        new SOAPHTTPTransport($endpoint),
        'urn:GoogleSearch'
      );

      self::registerMapping(
        new QName('urn:GoogleSearch', 'GoogleSearchResult'), 
        XPClass::forName('com.google.soap.search.GoogleSearchResult')
      );
      self::registerMapping(
        new QName('urn:GoogleSearch', 'ResultElement'), 
        XPClass::forName('com.google.soap.search.ResultElement')
      );
      self::registerMapping(
        new QName('urn:GoogleSearch', 'DirectoryCategory'), 
        XPClass::forName('com.google.soap.search.DirectoryCategory')
      );
    }

    /**
     * Invokes the method "doGetCachedPage"
     *
     * @access  public
     * @param   string key
     * @param   string url
     * @return  xml.soap.types.SOAPBase64Binary
     * @throws  xml.soap.SOAPFaultException in case a fault occurs
     * @throws  io.IOException in case an I/O error occurs
     * @throws  xml.FormatException in case not-well-formed XML is returned
     */
    public function doGetCachedPage($key, $url) {
      return self::invoke(
        'doGetCachedPage',
        new SOAPNamedItem('key', $key),
        new SOAPNamedItem('url', $url)
      );
    }

    /**
     * Invokes the method "doSpellingSuggestion"
     *
     * @access  public
     * @param   string key
     * @param   string phrase
     * @return  string
     * @throws  xml.soap.SOAPFaultException in case a fault occurs
     * @throws  io.IOException in case an I/O error occurs
     * @throws  xml.FormatException in case not-well-formed XML is returned
     */
    public function doSpellingSuggestion($key, $phrase) {
      return self::invoke(
        'doSpellingSuggestion',
        new SOAPNamedItem('key', $key),
        new SOAPNamedItem('phrase', $phrase)
      );
    }

    /**
     * Invokes the method "doGoogleSearch"
     *
     * @access  public
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
     * @return  &com.google.soap.search.GoogleSearchResult
     * @throws  xml.soap.SOAPFaultException in case a fault occurs
     * @throws  io.IOException in case an I/O error occurs
     * @throws  xml.FormatException in case not-well-formed XML is returned
     * @see     http://www.google.com/apis/reference.html#searchrequest Search Parameters 
     */
    public function doGoogleSearch($key, $q, $start, $maxResults, $filter, $restrict, $safeSearch, $lr, $ie, $oe) {
      return self::invoke(
        'doGoogleSearch',
        new SOAPNamedItem('key', $key),
        new SOAPNamedItem('q', $q),
        new SOAPNamedItem('start', $start),
        new SOAPNamedItem('maxResults', $maxResults),
        new SOAPNamedItem('filter', $filter),
        new SOAPNamedItem('restrict', $restrict),
        new SOAPNamedItem('safeSearch', $safeSearch),
        new SOAPNamedItem('lr', $lr),
        new SOAPNamedItem('ie', $ie),
        new SOAPNamedItem('oe', $oe)
      );
    }
  }
?>
