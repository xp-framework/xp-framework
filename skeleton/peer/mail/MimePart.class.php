<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */

  // Content-Disposition
  define('MIME_DISPOSITION_INLINE',     'inline');
  define('MIME_DISPOSITION_UNKNOWN',     '');
  define('MIME_DISPOSITION_ATTACHMENT', 'attachment');

  // Mime encodings
  define('MIME_ENC_BASE64',     'base64');
  define('MIME_ENC_QPRINT',     'quoted-printable');
  define('MIME_ENC_8BIT',       '8-bit');
  
  /**
   * MIME message part
   *
   * @see      rfc://1521
   * @purpose  Wrap
   */
  class MimePart extends Object {
    var
      $contenttype      = '',
      $charset          = '',
      $encoding         = '',
      $disposition      = '',
      $name             = '',
      $filename         = '',
      $id               = '',
      $body             = '',
      $headers          = array();
     
    /**
     * Constructor
     *
     * @access  public
     * @param   string body default ''
     * @param   string contenttype default ''
     * @param   string encoding default ''
     * @param   string name
     */ 
    function __construct(
      $body= '', 
      $contenttype= '',
      $encoding= '',
      $name= ''
    ) {
      $this->body= $body;
      $this->contenttype= $contenttype;
      $this->encoding= $encoding;
      
      // Some useful defaults here
      if ('' != ($this->name= $name)) {
        $this->filename= $this->name;
        $this->disposition= MIME_DISPOSITION_ATTACHMENT;
        $this->charset= '';
      } else {
        $this->charset= 'iso-8859-1';
      }
    }

    /**
     * Returns whether this part is an attachment
     *
     * @access  public
     * @return  bool TRUE if this part is an attachment
     */
    function isAttachment() {
      return (MIME_DISPOSITION_ATTACHMENT == $this->disposition);
    }

    /**
     * Returns whether this part is an inline
     *
     * @access  public
     * @return  bool TRUE if this part is an inline
     */
    function isInline() {
      return (
        (MIME_DISPOSITION_INLINE == $this->disposition) ||
        (MIME_DISPOSITION_UNKNOWN == $this->disposition)
      );
    }
     
    /**
     * Get part filename
     *
     * @access  public
     * @return  string
     */
    function getFilename() {
      return $this->filename;
    }

    /**
     * Set part filename
     *
     * @access  public
     * @param   string filename
     */
    function setFilename($filename) {
      $this->filename= $filename;
    }

    /**
     * Get part disposition
     *
     * @access  public
     * @return  string
     */
    function getDisposition() {
      return $this->disposition;
    }

    /**
     * Set part disposition
     *
     * @access  public
     * @param   string disposition
     */
    function setDisposition($disposition) {
      $this->disposition= $disposition;
    }

    /**
     * Get part name
     *
     * @access  public
     * @return  string
     */
    function getName() {
      return $this->name;
    }

    /**
     * Set part name
     *
     * @access  public
     * @param   string name
     */
    function setName($name) {
      $this->name= $name;
    }

    /**
     * Get part encoding
     *
     * @access  public
     * @return  string
     */
    function getEncoding() {
      return $this->encoding;
    }

    /**
     * Set part encoding
     *
     * @access  public
     * @param   string encoding
     */
    function setEncoding($encoding) {
      $this->encoding= $encoding;
    }

    /**
     * Get part contenttype
     *
     * @access  public
     * @return  string
     */
    function getContenttype() {
      return $this->contenttype;
    }

    /**
     * Set part contenttype
     *
     * @access  public
     * @param   string contenttype
     */
    function setContenttype($contenttype) {
      $this->contenttype= $contenttype;
    }
    
    /**
     * Retrieves the mimeparts content-id. If this mimepart
     * does not yet have a content-id, generateContentId() will
     * be used to create one.
     *
     * @access  public
     * @return  string id
     */
    function getContentId() {
      if (empty ($this->id))
        $this->generateContentId();
        
      return $this->id;
    }

    /**
     * Sets the content-id for this mimepart. The content-id
     * is used to reference this part within other mime-parts
     * and thus must be unique.
     *
     * @access  public
     * @param   string id
     */    
    function setContentId($id) {
      $this->id= $id;
    }
    
    /**
     * Generate a unique id for this mimepart.
     *
     * @access  public
     */
    function generateContentId() {
      $this->id= uniqid(time(), TRUE);
    }
      
    /**
     * Set part body
     *
     * @access  public
     * @param   string body
     * @param   bool encoded default FALSE
     */
    function setBody($body) {
      $this->body= $body;
    }
  
    /**
     * Get part body.
     *
     * @access  public
     * @param   decode default FALSE
     * @return  string
     */
    function getBody($d= FALSE) {
      if ($d && !empty ($this->encoding)) switch ($this->getEncoding()) {
        case MIME_ENC_BASE64:
          return base64_decode ($this->body);
        case MIME_ENC_QPRINT:
          return quoted_printable_decode ($this->body);
        case MIME_ENC_8BIT:
          return $this->body;
      }
      return $this->body;
    }
  
    /**
     * Set headers from string
     *
     * @access  public
     * @param   string str
     */
    function setHeaderString($str) {
      $t= strtok($str, "\n\r");
      while ($t) {
        if ("\t" != $t{0}) list($k, $t)= explode(': ', $t, 2);
        switch (ucfirst($k)) {
          case HEADER_CONTENTTYPE:
          case HEADER_ENCODING:
          case 'Content-Disposition':
            
            // Ignore, these are alreay set
            break;
          
          default:
            $this->headers[$k]= (isset($this->headers[$k]) ? $this->headers[$k]."\n\t" : '').$t;
        }
        $t= strtok("\n\r");
      }
    }
    
    /**
     * Return headers as string
     *
     * @access  public
     * @return  string headers
     */
    function getHeaderString() {
    
      // Content-Type: application/octet-stream; name="Document003.pif"
      // Content-Type: text/plain; charset="iso-8859-1"
      $h= 'Content-Type: '.$this->contenttype;
      if (!empty ($this->boundary)) {
        $h.= '; boundary= "'.$this->boundary.'"';
      }
      
      if (!empty($this->name)) {
        $h.= '; name='.$this->name;
      } else if (!empty($this->charset)) {
        $h.= '; charset="'.$this->charset.'"';
      }
      $h.= "\n";
      
      // Content-Transfer-Encoding: base64
      if (!empty($this->encoding)) $h.= "Content-Transfer-Encoding: {$this->encoding}\n";
      
      // Content-ID: <5249040$10425461803e23fe045e19e8.09373429>
      if (!empty($this->id)) $h.= "Content-ID: <{$this->id}>\n";
      
      // Content-Disposition: attachment; filename="foo.bar.baz"
      if (!empty($this->disposition)) {
        $h.= 'Content-Disposition: '.$this->disposition.(empty($this->filename) 
          ? ''
          : '; filename="'.$this->filename.'"'
        )."\n";
      }
      
      foreach ($this->headers as $k => $v) {
        $h.= $k.': '.$v."\n";
      }

      return $h;
    }
  }
?>
