<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */

  // Encodings
  define('MIME_ENC_BASE64',     'base64');
  define('MIME_ENC_QPRINT',     'quoted-printable');
  
  // Verschiedene Mime-Types
  define('MIME_TYPE_TEXT',      'text/plain');
  define('MIME_TYPE_HTML',      'text/html');
  define('MIME_TYPE_IMAGEGIF',  'image/gif');
  define('MIME_TYPE_IMAGEPNG',  'image/png');
  define('MIME_TYPE_IMAGEJPG',  'image/jpeg');
  
  /**
   * Repräsentiert einen Teil einer Multipart-MIME-Email
   *
   * @see net.mail.MimeMail
   * @see http://faqs.org/rfcs/rfc1521.html
   */
  class MimePart extends Object {
    var 
      $type             = MIME_TYPE_TEXT,       // Mime-Type
      $charset          = 'iso-8859-1',         // Charset
      $encoding         = '8-bit',              // Encoding, bspw base64
      $disposition,                             // Disposition: inline|attachment
      $name,                                    // Beschreibung
      $filename,                                // Dateiname
      $id,                                      // Content-ID
      $content;                                 // Part-Inhalt
    
    /**
     * Die Header eines Parts zurückgeben
     *
     * @access  public
     * @return  string Part-Header
     */ 
    function getHeader() {
      $partHeader= '';
      
      // Content-Type, Beschreibung, Dateiname, Charset
      if (isset($this->type)) $partHeader.= "Content-Type: {$this->type};\n";
      if (isset($this->charset)) $partHeader.= "\tcharset=\"{$this->charset}\"\n";
      if (isset($this->name)) $partHeader.= "\tname=\"{$this->name}\"\n";
      if (isset($this->filename)) $partHeader.= "\tfilename=\"{$this->filename}\"\n";

      // Encoding
      if (isset($this->encoding)) $partHeader.= "Content-Transfer-Encoding: {$this->encoding}\n";
      
      // ID
      if (isset($this->id)) $partHeader.= "Content-ID: <{$this->id}>\n";
      
      // Dispostion
      if (isset($this->disposition)) $partHeader.= "Content-Disposition: {$this->disposition}\n";
      
      return $partHeader;
    }
    
    /**
     * Den Inhalt entsprechend des Encoding kodieren
     * Wird der Parameter encoding weggelassen, wird die Membervariable encoding benutzt,
     * wird er definiert, so wird auch die Membervariable überschrieben
     *
     * @access  public
     * @param   int encoding default NULL Das zu verwendende Encoding-Verfahren
     */
    function encodeContent($encoding= NULL) {
      if (NULL != $encoding) $this->encoding= $encoding;
      
      switch ($this->encoding) {
        case MIME_ENC_BASE64: 
          $this->content= base64_encode($this->content); 
          break;
          
        case MIME_ENC_QPRINT: 
          $this->content= imap_qprint($this->content); 
          break;
      }
    }
    
    /**
     * Den Inhalt eines Parts zurückgeben
     *
     * @access  public
     * @param   bool encode default FALSE Encoding vornehmen (abhängig vom Member "encoding")
     * @return  string Inhalt
     */
    function getContent($encode= FALSE) {
      if (isset($encode)) $this->encodeContent();
      return $this->content;
    }
    
    /**
     * Den gesamten Part zurückgeben
     *
     * @access  public
     * @param   int encode default FALSE 
     * @see     net.mail.MimePart#getContent
     * @return  string String-Repräsentation des gesamten Parts
     */
    function getPart($encode= FALSE) {
      return (
        $this->getHeader().
        "\n".
        $this->getContent($encode).
        "\n"
      );
    }
  }
?>
