<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'com.google.search.custom.GoogleSearchQuery', 
    'com.google.search.custom.types.Response', 
    'peer.http.HttpConnection',
    'xml.meta.Unmarshaller',
    'io.streams.Streams'
  );

  /**
   * Entry point class to Google Custom Search
   * 
   * Example:
   * <code>
   *   $client= new GoogleSearchClient('http://gsa23.enterprisedemo-google.com/search');
   *   $response= $client->searchFor(create(new GoogleSearchQuery())
   *     ->withTerm('test')
   *     ->startingAt(10)
   *   );
   * </code>
   *
   * @see   http://www.google.com/cse/docs/resultsxml.html
   */
  class GoogleSearchClient extends Object {
    protected $conn= NULL;
    
    /**
     * Constructor
     *
     * @param   var conn either a HttpConnection or a string
     */
    public function __construct($conn) {
      $this->conn= $conn instanceof HttpConnection ? $conn : new HttpConnection($conn);
    }
    
    /**
     * Executes a search and return the results
     *
     * @param   com.google.search.custom.GoogleSearchQuery query
     * @return  com.google.search.custom.types.Response
     */
    public function searchFor(GoogleSearchQuery $query) {
    
      // Build query parameter list
      $params= array(
        'q'      => $query->getTerm(),
        'output' => 'xml_no_dtd'
      );
      
      // Optional: Start (0- based)
      ($s= $query->getStart()) && $params['start']= $s;

      // Retrieve result as XML
      return Unmarshaller::unmarshal(
        Streams::readAll($this->conn->get($params)->getInputStream()),
        'com.google.search.custom.types.Response'
      );
    }
  }
?>
