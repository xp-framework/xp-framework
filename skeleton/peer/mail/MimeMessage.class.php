<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */
 
  uses('peer.mail.Message', 'peer.mail.MimePart');
  
  define('HEADER_MIMEVER',      'MIME-Version');
  
  /**
   * Mail message
   *
   * @see
   * @purpose  Wrap
   */
  class MimeMessage extends Message {
    var
      $parts     = array(),
      $mimever   = '1.0',
      $boundary  = '';

    /**
     * Constructor. Also generates a boundary of the form
     * <pre>
     * ----=_NextPart_10424693873e22d20b43b490.00112051@foo.bar.baz
     * </pre>
     *
     * @access  public
     */
    function __construct() {
      $this->setBoundary('----=_NextPart_'.uniqid(time(), TRUE).'@'.getenv('HOSTNAME'));
      $this->header[HEADER_MIMEVER]= $this->mimever;
      parent::__construct();
    }

    /**
     * Add a Mime Part
     *
     * @access  public
     * @param   &peer.mail.MimePart part
     * @throws  IllegalArgumentException if part argument is not a peer.mail.MimePart
     */
    function addPart(&$part) {
      if (!is_a($part, 'MimePart')) {
        trigger_error('Given type: '.get_class($part), E_USER_NOTICE);
        return throw(new IllegalArgumentException(
          'Parameter part is not a peer.mail.MimePart'
        ));
      }
      $this->parts[]= &$part;
    }
    
    /**
     * Set boundary and updates Content-Type header. Note: A boundary is generated 
     * upon instanciation, so this is usually not needed!
     *
     * @access  public
     * @param   string b the new boundary
     */
    function setBoundary($b) {
      $this->boundary= $b;
      $this->contenttype= "multipart/mixed;\n\tboundary=\"".$this->boundary.'"';
    }

    /**
     * Get boundary
     *
     * @access  public
     * @return  string
     */
    function getBoundary() {
      return $this->boundary;
    }
    
    /**
     * Set message body
     *
     * @access  public
     * @param   string body
     */
    function setBody() {
      // TBD: Split up from string
    }

    /**
     * Get message body.
     *
     * @see     xp://peer.mail.Message#getBody
     * @access  public
     * @return  string
     */
    function getBody() {
      if (NULL !== $this->folder && NULL === $this->body) {
        $this->setBody($this->folder->getMessagePart($this->uid, '1'));
      }
      
      $body= "This is a multi-part message in MIME format.\n\n";
      for ($i= 0, $s= sizeof($this->parts); $i < $s; $i++) {
        $body.= (
          '--'.$this->boundary."\n".
          $this->parts[$i]->getHeaderString().
          "\n".
          $this->parts[$i]->getBody().
          "\n"
        );
      }
      
      // End boundary
      return $body.'--'.$this->boundary."--\n";
    }

  }
?>
