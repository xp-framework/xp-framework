<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */

  // Content-Disposition
  define('MIME_DISPOSITION_INLINE',     'inline');
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
      $contenttype      = 'text/plain',
      $charset          = 'iso-8859-1',
      $encoding         = MIME_ENC_8BIT,
      $disposition      = MIME_DISPOSITION_INLINE,
      $name             = '',
      $filename         = '',
      $id               = '',
      $body             = '';

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
      return (MIME_DISPOSITION_INLINE == $this->disposition);
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
     * @return  string
     */
    function getBody() {
      return $this->body;
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
      if (!empty($this->name)) {
        $h.= ";\n\t".'name='.$this->name;
      } else {
        $h.= ";\n\t".'charset='.$this->charset;
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
          : ";\n\t".'filename="'.$this->filename.'"'
        )."\n";
      }
      return $h;
    }
  }
?>
