<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class FormData extends Object {
    const
      CRLF  = "\r\n",
      DEFAULT_CONTENTTYPE = 'text/plain',
      DEFAULT_CHARSET     = 'iso-8859-1';

    protected
      $name         = NULL,
      $content      = NULL,
      $contentType  = NULL,
      $charset      = NULL;

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function __construct($name, $content, $contentType= self::DEFAULT_CONTENTTYPE, $charset= self::DEFAULT_CHARSET) {
      $this->name= $name;
      $this->content= $content;
      $this->contentType= $contentType;
      $this->charset= $charset;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function getData() {
      $s= 'Content-Disposition: form-data; name="'.$this->name.'"'.self::CRLF;
      if (self::DEFAULT_CONTENTTYPE != $this->contentType || self::DEFAULT_CHARSET != $this->charset) {
        $s.= 'Content-Type: '.$this->contentType;
        
        if (self::DEFAULT_CHARSET != $this->charset) {
          $s.= '; charset="'.$this->charset.'"';
        }
        
        $s.= self::CRLF;
      }
      
      return $s.self::CRLF.$this->content;
    }
  }
?>
