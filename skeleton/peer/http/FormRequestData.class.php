<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.http.RequestData', 'peer.http.FormData', 'peer.Header');

  /**
   * Build an HttpRequest w/ embedded multipart/form-data
   *
   * Example:
   * <code>
   *   $request= $conn->create(new HttpRequest());
   *   $request->setMethod(HttpConstants::POST);
   *   $request->setParameters(create(new FormRequestData())
   *     ->withPart(new FormData('key', 'value'))
   *     ->withPart(new FormData('comment.txt', $contents, 'text/plain', 'utf-8'))
   *   );
   * </code>
   *
   * @see   xp://peer.http.HttpConnection
   * @see   xp://peer.http.HttpRequest
   * @see   xp://peer.http.FormData
   * @test  xp://net.xp_framework.unittest.peer.http.FormRequestdataTest
   */
  class FormRequestData extends RequestData {
    const
      CRLF  = "\r\n";

    protected
      $parts      = array(),
      $boundary   = NULL;

    /**
     * Constructor
     *
     * @param   peer.http.FormData[] parts default array()
     */
    public function __construct($parts= array()) {
      $this->boundary= '__--boundary-'.uniqid(time()).'--__';

      foreach ($parts as $part) {
        $this->addPart($part);
      }
    }
    
    /**
     * Retrieve boundary
     *
     * @return  string
     */
    public function getBoundary() {
      return $this->boundary;
    }    
    
    /**
     * Add form part
     *
     * @param   peer.http.FormData item
     * @return  peer.http.FormData
     */
    public function addPart(FormData $item) {
      $this->parts[]= $item;
      return $item;
    }

    /**
     * Add form part - fluent interface
     *
     * @param   peer.http.FormData item
     * @return  peer.http.FormRequestData this
     */
    public function withPart(FormData $item) {
      $this->parts[]= $item;
      return $this;
    }
    
    /**
     * Retrieve headers to be set
     *
     * @return  peer.Header[]
     */
    public function getHeaders() {
      $headers= parent::getHeaders();
      $headers[]= new Header('Content-Type', 'multipart/form-data; boundary='.$this->boundary);
      return $headers;
    }
    
    /**
     * Retrieve data for request
     *
     * @return  string
     */
    public function getData() {
      $ret= self::CRLF.'--'.$this->boundary;
      
      foreach ($this->parts as $part) {
        $ret.= 
          self::CRLF.$part->getData().
          self::CRLF.'--'.$this->boundary
        ;
      }
      
      return $ret.'--'.self::CRLF;
    }
  }
?>
