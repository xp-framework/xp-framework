<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */
 
  // Common Header
  define('HEADER_FROM',         'From');
  define('HEADER_TO',           'To');
  define('HEADER_CC',           'Cc');
  define('HEADER_BCC',          'Bcc');
  define('HEADER_SUBJECT',      'Subject');
  define('HEADER_PRIORITY',     'X-Priority');
  define('HEADER_ENCODING',     'Content-Transfer-Encoding');
  define('HEADER_CONTENTTYPE',  'Content-Type');
  
  // Priorities
  define('MAIL_PRIORITY_LOW',           0x0005);
  define('MAIL_PRIORITY_NORMAL',        0x0003);
  define('MAIL_PRIORITY_HIGH',          0x0001);
  
  // Für die Funktion getHeaders()
  define('HEADER_ALL',        0x0000);  // Alle
  define('HEADER_COMPAT',     0x0001);  // Mail-Header, die bei der Mail-Funktion sonst doppelt auftreten, wegschmeißen:)
  
  /**
   * Repräsentiert eine "Basic"-Email
   *
   * @see http://www.faqs.org/rfcs/rfc822.html
   */
  class Mail extends Object {
    var 
      $header           = array(),
      $body             = '';
      
    // Komfort-Member-Variablen, könnte man auch über Header abbilden
    // Sinnige Default-Werte setzen
    var
      $to               = array(),
      $from             = array(),
      $cc               = array(),
      $bcc              = array(),
      $subject          = '',
      $priority         = MAIL_PRIORITY_NORMAL,
      $contenttype      = 'text/plain; charset=iso-8859-1',
      $encoding         = '8bit';

    /**
     * Die Adressliste für die Darstellung als Header zurückgeben.
     * Beispiel
     * ============================================
     * To: Timm Friebe <friebe@schlund.de>,
     *          Alex Kiesel <kiesel@schlund.de>,
     *          Jens Strobel <strobel@schlund.de>
     * ============================================
     * => Weitere Zeilen fangen mit \n\t an
     *
     * @access  public
     * @param   array list Liste von Mailadressen
     * @return  string Entsprechend obigem Beispiel formatierter Header-Wert
     */    
    function getAddressList($list) {
      $return= '';
      foreach($list as $element) {
        $return.= "\n\t".$element;
      }
      return substr($return, 2);
    }
    
    /**
     * Einen Header hinzufügen, bspw: $mail->addHeader('X-Binford', '6100 (more power)');
     * Daraus wird dann X-Binford: 6100 (more power)
     *
     * @access  public
     * @param   string header Der Header
     * @param   string value Der Header-Wert
     * @return  bool TRUE := Der Header wurde gesetzt. Manche speziellen Header 
     *          (From, To, Cc, Bcc) können nicht über diese Funktion gesetzt werden
     */
    function addHeader($header, $value) {
      if (
        HEADER_FROM == $header or
        HEADER_TO   == $header or
        HEADER_CC   == $header or
        HEADER_BCC  == $header
      ) return FALSE;
      
      $this->header[$header]= $value;
      return TRUE;
    }

    /**
     * Gibt den Body-Part einer Mail zurück
     *
     * @access  public
     * @return  string Kompletter Body
     */
    function getBody() {
      return $this->body;
    }
    
    /**
     * Gibt einen bestimmten Header zurück
     *
     * @access  public
     * @param   string name Header-Name
     * @return  string Header-Wert oder NULL, wenn dieser Header nicht existiert
     */
    function getHeader($name) {
      return isset($this->header[$name]) ? $this->header[$name] : NULL;
    }
    
    /**
     * Private Helper-Funktion, überschreibt Header, falls sie nicht gesetzt sind,
     * mit den entsprechenden "Komfort-Member-Variablen"
     *
     * @access  private
     * @param   string key Header-Name
     * @param   string value Header-Wert
     * @return  
     */
    function _setHeaderIfEmpty($key, $value) {
      if (empty($this->header[$key])) $this->header[$key]= $value;
    }
    
    /**
     * Gibt die Header als String zurück. Die PHP-Funktion mail() kommt durcheinander,
     * wenn man To: und Subject: im vierten Parameter mitübergibt und schreibt diese
     * dann doppelt in die Mail. Daher können diese mit dem Modus HEADER_COMPAT 
     * "unterdrückt" werden
     *
     * @access  public
     * @param   int mode default HEADER_ALL Rückgabe-Modus: Alle Header oder PHP-mail() kompatibel
     * @return  string String-Repräsentation der Header
     */
    function getHeaders($mode= HEADER_ALL) {
      $header= '';
      
      // Spezial-Header
      $this->header[HEADER_BCC]=  $this->getAddressList($this->bcc);
      $this->header[HEADER_CC]=   $this->getAddressList($this->cc);
      $this->header[HEADER_TO]=   $this->getAddressList($this->to);
      $this->header[HEADER_FROM]= $this->getAddressList($this->from);

      // Wenn nicht über addHeader() gesetzt, "Komfort-Member" benutzen
      $this->_setHeaderIfEmpty(HEADER_PRIORITY, $this->priority);
      $this->_setHeaderIfEmpty(HEADER_SUBJECT, $this->subject);
      $this->_setHeaderIfEmpty(HEADER_ENCODING, $this->encoding);
      $this->_setHeaderIfEmpty(HEADER_CONTENTTYPE, $this->contenttype);
      
      // Kompatibilitätsmodus für die mail()-Funktion, da dies sonst doppelt vorkommt:)
      if ($mode== HEADER_COMPAT) {
        unset($this->header[HEADER_TO]);
        unset($this->header[HEADER_SUBJECT]);
      }
      
      // Userdefinierte Header nach unten
      $this->header= array_reverse($this->header);

      // Header-Source (leere Header discarden)
      foreach ($this->header as $key=> $val) {
        if (empty($val)) continue;
        $header.= sprintf("%s: %s\n", $key, $val);
      }
      return $header;
    }
    
    /**
     * Die Komplette Mail als String zurückgebeb
     *
     * @access  public
     * @param   int mode default HEADER_ALL
     * @see     net.mail.Mail#getHeader
     * @return  string String-Repräsentation der kompletten Mail
     */
    function getMail($mode= HEADER_ALL) {
      return (
        $this->getHeaders($mode).
        "\n".
        $this->getBody()
      );
    }
  } 
?>
