<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */
 
  uses(
    'peer.mail.Message', 
    'peer.mail.MimePart',
    'peer.mail.MultiPart'
  );
  
  /**
   * Mail message
   *
   * @see
   * @purpose  Wrap
   */
  class MimeMessage extends Message {
    var
      $parts     = array(),
      $encoding  = '',
      $boundary  = '';
      
    var
      $_ofs      = 0;

    /**
     * Constructor. Also generates a boundary of the form
     * <pre>
     * ----=_Part_10424693873e22d20b43b490.00112051
     * </pre>
     *
     * @access  public
     */
    function __construct($uid= -1) {
      $this->setBoundary('----=_Part_'.uniqid(time(), TRUE));
      $this->headers[HEADER_MIMEVER]= $this->mimever;
      parent::__construct($uid);
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
      $this->contenttype= 'multipart/mixed; boundary="'.$this->boundary.'"';
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
     * Return headers as string
     *
     * @access  public
     * @return  string headers
     */
    function getHeaderString() {
      if (1 == sizeof($this->parts) && $this->parts[0]->isInline()) {
        $this->setContenttype($this->parts[0]->getContenttype());
        $this->charset= $this->parts[0]->charset;
      }
      
      return parent::getHeaderString();
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function _lookupattr($parameters, $val) {
      if (!is_array($parameters)) return FALSE;
      
      for ($i= 0, $s= sizeof($parameters); $i < $s; $i++) {
        if (0 == strcasecmp($parameters[$i]->attribute, $val)) {
          return $parameters[$i]->value;
        }
      }
      
      return FALSE;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function _recurseparts(&$parts, &$p, $id= '') {
      static $types= array(
        'text',
        'multipart',
        'message',
        'application',
        'audio',
        'image',
        'video',
        'unknown'
      );
      
      for ($i= 0, $s= sizeof($p); $i < $s; $i++) {
        $pid= sprintf('%s%d', $id, $i+ 1);
        
        if (empty($p[$i]->parts)) {
          $part= &new MimePart(
            NULL,
            NULL,
            $this->_lookupattr(@$p[$i]->parameters, 'CHARSET'),
            $this->_lookupattr(@$p[$i]->dparameters, 'NAME')
          );
        } else {
          $part= &new MultiPart();
        }
        $part->setContentType($types[$p[$i]->type].'/'.strtolower($p[$i]->subtype));
        $part->setDisposition($p[$i]->ifdisposition 
          ? MIME_DISPOSITION_ATTACHMENT 
          : MIME_DISPOSITION_INLINE
        );
        if (count ($p[$i]->dparameters)) foreach ($p[$i]->dparameters as $dp) {
          if ('filename' == strtolower ($dp->attribute) && !empty ($dp->value))
            $part->setFilename ($dp->value);
        }
        $part->id= $pid;
        
        // We can retreive the body here since the message has been read anyway
        if (!empty($p[$i]->parts)) {
          if ($p[$i]->ifsubtype) switch ($p[$i]->subtype) {
            case 'MIXED': 
              $pid= substr($pid, 0, -2); 
              break;
              
            default: // Nothing
          }

          // Recurse through parts
          $this->_recurseparts($part->parts, $p[$i]->parts, $pid.'.');
          
          // Multipart -> part.0 are the headers
          $part->parts[0]->setHeaderString($this->folder->getMessagePart($this->uid, $pid.'.0'));

        } else {
          $part->body= $this->folder->getMessagePart($this->uid, $pid);
        }
        
        #ifdef DEBUG
        # var_dump($p[$i]);
        # echo '['.$pid.']:: '; var_dump($part);
        #endif
        
        $part->folder= &$this->folder;
        $parts[]= &$part;
      }
    }
    
    /**
     * Get a part
     *
     * @access  public
     * @param   int id default -1
     * @return  &peer.mail.MimePart part
     */
    function &getPart($id= -1) {
      $this->_parts();
      
      // Iterative use
      if (-1 == $id) $id= $this->_ofs++;
      
      // EOL
      if (!isset($this->parts[$id])) {
        $this->_ofs= 0;
        return NULL;
      }
      
      return $this->parts[$id];
    }
    
    /**
     * Get structure from folder
     *
     * @access  private
     * @return  bool got parts
     */    
    function _parts() {
      if ((NULL === $this->folder) || (!empty($this->parts))) return FALSE;
      
      $struct= &$this->folder->getMessageStruct($this->uid);
      if (!$struct->parts) {
        // var_dump($struct);
        return FALSE;
      }
      
      $this->_recurseparts($this->parts, $struct->parts);
    }

    /**
     * Get message body.
     *
     * @see     xp://peer.mail.Message#getBody
     * @access  public
     * @return  string
     */
    function getBody() {
      $this->_parts();
      $body= "This is a multi-part message in MIME format.\n\n";
      
      if (1 == ($size= sizeof($this->parts)) && $this->parts[0]->isInline()) {
        return $body.$this->parts[0]->getBody();
      }
      
      for ($i= 0; $i < $size; $i++) {
        $body.= (
          '--'.$this->boundary."\n".
          $this->parts[$i]->getHeaderString().
          "\n".
          $this->parts[$i]->getBody().
          "\n\n"
        );
      }
      
      // End boundary
      return $body.'--'.$this->boundary."--\n";
    }

  }
?>
