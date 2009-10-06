<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.http.RequestData', 'peer.http.FormData');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class FormRequestData extends RequestData {
    const
      CRLF  = "\r\n";

    protected
      $parts      = array(),
      $boundary   = NULL;

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function __construct($parts= array()) {
      $this->boundary= '__--boundary-'.uniqid(time()).'--__';

      foreach ($parts as $part) {
        $this->addPart($part);
      }
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function getBoundary() {
      return $this->boundary;
    }    
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function addPart(FormData $item) {
      $this->parts[]= $item;
      return $item;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function getHeaders() {
      $headers= parent::getHeaders();
      $headers[]= new Header('Content-Type', 'multipart/form-data; boundary='.$this->boundary);
      return $headers;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
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
