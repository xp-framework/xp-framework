<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */

  uses('net.mail.Mail');
  
  define('HEADER_MIMEVER',  'MIME-Version');
  
  /**
   * Repräsentiert eine Multipart-MIME-Email
   *
   * @see http://faqs.org/rfcs/rfc1521.html
   */
  class MimeMail extends Mail {
    var 
      $parts     = array(),
      $mimever   = '1.0',
      $boundary;
      
    /**
     * Constructor
     *
     * @access  public
     */
    function __construct() {
      parent::__construct();

      // Trenner als Zufallswert berechnen
      $this->boundary= '----=_NextPart_'.md5(uniqid(time())).'@'.getenv('HOSTNAME');
    }
        
    /**
     * Ein "Part" hinzufügen
     *
     * @access  public
     * @param   net.mail.MimePart part Ein Mime-Part-Objekt
     */
    function addPart(&$part) {
      $this->parts[]= &$part;
    }
   
    /**
     * Gibt die Header als String zurück, im Falle, dass nur ein Part vorhanden ist,
     * ohne multipart/mixed, ansonsten mit 
     *
     * @access  public
     * @param   int mode default HEADER_ALL Rückgabe-Modus: Alle Header oder PHP-mail() kompatibel
     * @return  string String-Repräsentation der Header
     * @see     net.mail.Mail#getHeaders
     */ 
    function getHeaders($mode= HEADER_ALL) {
      $this->_setHeaderIfEmpty(HEADER_MIMEVER, $this->mimever);
    
      // Single-Part?
      $this->addHeader('Content-Type', (sizeof($this->parts)== 1) 
        ? $this->contenttype
        : "multipart/mixed;\n\tboundary=\"".$this->boundary.'"'
      );
      
      // Alle Parts können nochmal Einfluss auf die Mail ansich nehmen
      for ($i= 0; $i< sizeof($this->parts); $i++) {
        if (method_exists($this->parts[$i], '_pCallModifyMail')) {
          $this->parts[$i]->_pCallModifyMail($this);
        }
      }
        
      return parent::getHeaders($mode);
    }
    
    /**
     * Gibt den Body-Part einer Mail zurück, im Falle, dass nur ein Part vorhanden ist,
     * als multipart/mixed mit Signatur "This is...", anonsten ohne
     *
     * @access  public
     * @return  string Kompletter Body
     */
    function getBody() {
      // Single-Part?
      if (sizeof($this->parts)== 1) {
        return $this->parts[0]->getContent();
      }
      
      $body= "This is a multi-part message in MIME format.\n\n";
      for ($i= 0; $i< sizeof($this->parts); $i++) {
        $body.= (
          '--'.$this->boundary."\n".
          $this->parts[$i]->getPart()
        );
      }
      return (
        $body.
        '--'.$this->boundary."--\n"
      );
    }
  }
?>
