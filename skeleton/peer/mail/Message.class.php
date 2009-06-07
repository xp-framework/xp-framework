<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */

  uses('peer.mail.InternetAddress', 'util.Date');

  // Flags
  define('MAIL_FLAG_ANSWERED',      0x0001);
  define('MAIL_FLAG_DELETED',       0x0002);
  define('MAIL_FLAG_DRAFT',         0x0004);
  define('MAIL_FLAG_FLAGGED',       0x0008);
  define('MAIL_FLAG_RECENT',        0x0010);
  define('MAIL_FLAG_SEEN',          0x0020);
  define('MAIL_FLAG_USER',          0x0030);

  // Priorities
  define('MAIL_PRIORITY_LOW',       0x0005);
  define('MAIL_PRIORITY_NORMAL',    0x0003);
  define('MAIL_PRIORITY_HIGH',      0x0001);
  
  // Recipient type for addRecipient, addRecipients and getRecipients
  define('TO',  'to');
  define('CC',  'cc');
  define('BCC', 'bcc');

  // Common Header
  define('HEADER_FROM',         'From');
  define('HEADER_TO',           'To');
  define('HEADER_CC',           'Cc');
  define('HEADER_BCC',          'Bcc');
  define('HEADER_SUBJECT',      'Subject');
  define('HEADER_PRIORITY',     'X-Priority');
  define('HEADER_ENCODING',     'Content-Transfer-Encoding');
  define('HEADER_CONTENTTYPE',  'Content-Type');
  define('HEADER_DATE',         'Date');
  define('HEADER_MIMEVER',      'Mime-Version');
  define('HEADER_MESSAGEID',    'Message-ID');
  define('HEADER_RETURNPATH',   'Return-Path');
  
  /**
   * This class models an email message.
   *
   * Usage [creating a new message]:
   * <code>
   *   $m= new Message();
   *   $m->setFrom(new InternetAddress('friebe@example.com', 'Timm Friebe'));
   *   $m->addRecipient(TO, new InternetAddress('foo@bar.baz', 'Foo Bar'));
   *   $m->addRecipient(CC, new InternetAddress('timm@foo.bar', 'Timm Friebe'));
   *   $m->setHeader('X-Binford', '6100 (more power)');
   *   $m->setSubject('Hello world');
   *   $m->setBody('Testmail');
   *
   *   echo $m->getHeaderString()."\n".$m->getBody();
   * </code>
   *
   * @see      rfc://2822
   * @see      rfc://1896
   * @see      rfc://2045
   * @see      rfc://2046
   * @see      rfc://2047
   * @see      rfc://2048
   * @see      rfc://2049
   * @see      xp://peer.mail.MimeMessage
   * @see      xp://peer.mail.transport.Transport
   * @purpose  Provide a basic e-mail message (single-part)
   */
  class Message extends Object {
    public 
      $headers          = array(),
      $body             = '',
      $to               = array(),
      $from             = NULL,
      $cc               = array(),
      $bcc              = array(),
      $subject          = '',
      $priority         = MAIL_PRIORITY_NORMAL,
      $contenttype      = 'text/plain',
      $charset          = 'iso-8859-1',
      $encoding         = '8bit',
      $folder           = NULL,
      $uid              = 0,
      $flags            = 0,
      $size             = 0,
      $mimever          = '1.0',
      $date             = NULL,
      $message_id       = '',
      $returnpath       = '';
      
    public
      $_headerlookup    = NULL;
      
    /**
     * Constructor
     *
     * @param   int uid default -1
     */
    public function __construct($uid= -1) {
      $this->uid= $uid;
      $this->date= new Date();
    }
    
    /**
     * Set Mime version
     *
     * @param   string mimeversion
     */
    public function setMimeVersion($mimeversion) {
      $this->mimever= $mimeversion;
    }

    /**
     * Get Mime version
     *
     * @return  string
     */
    public function getMimeVersion() {
      return $this->mimever;
    }

    /**
     * Set Returnpath
     *
     * @param   string returnpath
     */
    public function setReturnPath($returnpath) {
      $this->returnpath= $returnpath;
    }

    /**
     * Get Returnpath
     *
     * @return  string
     */
    public function getReturnPath() {
      return $this->returnpath;
    }

    
    /**
     * Create string representation. Note: This is not suitable for sending mails,
     * use the getHeaderString() and getBody() methods!
     *
     * Example output:
     * <pre>
     * peer.mail.Message[10332605]@{
     *   [headers     ] array (
     *     'Received' => 'from moutvdom01.kundenserver.de by mx08.web.de with smtp
     *         (freemail 4.2.2.3 #20) id m15snxT-008AX0A; Sun, 14 Oct 2001 18:17 +0200
     *   from [195.20.224.209] (helo=mrvdom02.schlund.de)
     *         by moutvdom01.kundenserver.de with esmtp (Exim 2.12 #2)
     *         id 15snxT-0004WT-00
     *         for timm.friebe@bar.foo; Sun, 14 Oct 2001 18:17:47 +0200
     *   from p3e9e72f0.dip0.t-ipconnect.de ([62.158.114.240] helo=banane)
     *         by mrvdom02.schlund.de with smtp (Exim 2.12 #2)
     *         id 15snxK-0005Xj-00; Sun, 14 Oct 2001 18:17:38 +0200',
     *     'Message-ID' => '<013901c154cb$c03222d0$917cfea9@banane>',
     *     'MIME-Version' => '1.0',
     *     'X-MSMail-Priority' => 'Normal',
     *     'X-Mailer' => 'Microsoft Outlook Express 6.00.2505.0000',
     *     'X-MimeOLE' => 'Produced By Microsoft MimeOLE V6.00.2505.0000',
     *   )
     *   [body        ] ''
     *   [to          ] array (
     *     0 => 
     *     array (
     *       'personal' => '',
     *       'localpart' => 'timm.friebe',
     *       'domain' => 'bar.foo',
     *     ),
     *   )
     *   [from        ] =?iso-8859-1?Q?"Timm_Friebe"?= <thekid@example.foo>
     *   [cc          ] array (
     *     0 => 
     *     array (
     *       'personal' => '',
     *       'localpart' => 'thekid',
     *       'domain' => 'bar.baz',
     *     ),
     *   )
     *   [bcc         ] array (
     *   )
     *   [subject     ] 'Test mit HTML'
     *   [priority    ] 3
     *   [contenttype ] 'multipart/mixed; boundary="DORIS009DA9BD"'
     *   [encoding    ] '8bit'
     *   [folder      ] peer.mail.MailFolder[INBOX]@{
     *   name  -> peer.mail.store.ImapStore
     *   cache -> peer.mail.store.StoreCache[5]@{
     *     [folder/INBOX            ] object [mailfolder]
     *     [list/message/INBOX1     ] object [message]
     *     [list/message/INBOX2     ] object [message]
     *     [list/message/INBOX3     ] object [message]
     *     [list/message/INBOX5     ] object [message]
     *   }
     * }
     *   [uid         ] 10332605
     *   [flags       ] 32
     *   [size        ] 27124
     *   [date        ] Sun, 14 Oct 2001 18:17:38 +0200
     * }
     * </pre>
     *
     * @return  string
     */
    public function toString() {
      $s= '';
      $vars= get_object_vars($this);
      foreach (array_keys($vars) as $var) {
        if ('_' == $var{0}) continue;
        $s.= sprintf("  [%-12s] %s\n", $var, xp::stringOf($vars[$var], '  '));
      }
      return $this->getClassName().'['.$this->uid."]@{\n".$s."}\n";
    }

    /**
     * Sets message size
     *
     * @param   int size
     */
    public function setSize($size) {
      $this->size= $size;
    }
    
    /**
     * Retrieve message size
     *
     * @return  int
     */
    public function getSize() {
      return $this->size;
    }

    /**
     * Sets message-id
     *
     * @param   string message_id
     */
    public function setMessageId($message_id) {
      $this->message_id= $message_id;
    }
    
    /**
     * Retrieve message-id
     *
     * @return  string
     */
    public function getMessageId() {
      return $this->message_id;
    }
    
    /**
     * Sets message date
     *
     * @param   mixed arg
     */
    public function setDate($arg) {
      if ($arg instanceof Date) $this->date= $arg; else $this->date= new Date($arg);
    }
    
    /**
     * Retrieve message date
     *
     * @return  util.Date
     */
    public function getDate() {
      return $this->date;
    }
  
    /**
     * Add recipient
     *
     * @param   string type one of the constants TO, CC, BCC
     * @param   peer.mail.InternetAddress adr address to add
     */
    public function addRecipient($type, $adr) {
      $this->{$type}[]= $adr;
    }
    
    /**
     * Add recipients
     *
     * @param   string type one of the constants TO, CC, BCC
     * @param   peer.mail.InternetAddress[] adr addresses to add
     */
    public function addRecipients($type, $adr) {
      $this->{$type}= array_merge($this->{$type}, $adr);
    }

    /**
     * Get recipients
     *
     * @param   string type one of the constants TO, CC, BCC
     * @return  peer.mail.InternetAddress[] adr recipients of type
     */
    public function getRecipients($type) {
      return $this->{$type};
    }
    
    /**
     * Get recipient (use iteratively):
     * <code>
     *   while ($r= $m->getRecipient(TO)) {
     *     var_dump($r);
     *   }
     * </code>
     *
     * @param   string type one of the constants TO, CC, BCC
     * @return  peer.mail.InternetAddress[] adr recipients of type
     */
    public function getRecipient($type) {
      static $ofs= array();
      
      if (!isset($ofs[$type])) $ofs[$type]= 0;
      $l= $this->$type;
      if (!isset($l[$ofs[$type]])) {
        unset($ofs[$type]);
        return FALSE;
      }
      return $l[$ofs[$type]++];
    }
    
    /**
     * Set from
     *
     * @param   peer.mail.InternetAddress[] adr addresses to add
     */
    public function setFrom($adr) {
      $this->from= $adr;
    }

    /**
     * Get recipients
     *
     * @return  peer.mail.InternetAddress[] adr addresses to add
     */
    public function getFrom() {
      return $this->from;
    }
    
    /**
     * Private helper function
     *
     * @param   string header
     * @param   string value
     * @param   mixed add default FALSE
     * @return  bool TRUE if operation was successfull
     */
    protected function _setHeader($header, $value, $add= FALSE) {
      static $notallowed= array(HEADER_FROM, HEADER_TO, HEADER_CC, HEADER_BCC);
      
      if (in_array(ucfirst($header), $notallowed)) return FALSE;
      if ($add && isset($this->headers[$header])) {
        $this->headers[$header].= $add.$value;
      } else {
        $this->headers[$header]= $value;
      }
    }

    /**
     * Set additional headers. Note: This function cannot be used
     * to set From, To, Cc or Bcc!
     *
     * Example:
     * <code>
     *   $mail->setHeader('X-Binford', '6100 (more power)');
     * </code>
     *
     * @param   string header
     * @param   string value
     * @throws  lang.IllegalArgumentException if one of From, To, Cc or Bcc is specfied as header
     */
    public function setHeader($header, $value) {
      if (FALSE === $this->_setHeader($header, $value)) {
        throw new IllegalArgumentException('You cannot use this method to set '.$header);
      }
    }
    
    /**
     * Get header. The search is performed case-insensitively, so 
     * $mail->getHeader('Content-type') and $mail->getHeader('Content-Type')
     * will yield the same result.
     *
     * @param   string header a header name to look for
     * @return  string value header value or NULL to indicate the header doesn't exist
     */
    public function getHeader($header) {
      $header= strtolower($header);
      if (!isset($this->_headerlookup)) {
        $this->_headerlookup= array_change_key_case($this->headers, CASE_LOWER);
      }
      
      return isset($this->_headerlookup[$header]) ? $this->_headerlookup[$header] : NULL;
    }
    
    /**
     * Get message body. If this message is contained in a folder and the body
     * has'nt been fetched yet, it'll be retrieved from the storage underlying
     * the folder.
     *
     * @return  string
     */
    public function getBody() {
      if (
        (NULL === $this->folder) ||
        (NULL !== $this->body)
      ) return $this->body;
      
      // We have a folder and the body is NULL (indicating we haven't fetched this message)
      return ($this->body= $this->folder->getMessagePart($this->uid, '1'));
    }

    /**
     * Set message body
     *
     * @param   string body
     */
    public function setBody($body) {
      $this->body= $body;
    }

    /**
     * Get message subject
     *
     * @return  string
     */
    public function getSubject() {
      return $this->subject;
    }

    /**
     * Set message subject
     *
     * @param   string subject
     */
    public function setSubject($subject) {
      $this->subject= $subject;
    }

    /**
     * Get message encoding
     *
     * @return  string
     */
    public function getEncoding() {
      return $this->encoding;
    }

    /**
     * Set message encoding
     *
     * @param   string encoding
     */
    public function setEncoding($encoding) {
      $this->encoding= $encoding;
    }

    /**
     * Get message charset
     *
     * @return  string
     */
    public function getCharset() {
      return $this->charset;
    }

    /**
     * Set message charset
     *
     * @param   string charset
     */
    public function setCharset($charset) {
      $this->charset= $charset;
    }

    /**
     * Get message contenttype
     *
     * @return  string
     */
    public function getContenttype() {
      return $this->contenttype;
    }

    /**
     * Set message contenttype
     *
     * @param   string contenttype
     */
    public function setContenttype($contenttype) {
      $this->contenttype= $contenttype;
    }
    
    /**
     * Set headers from string
     *
     * @param   string str
     */
    public function setHeaderString($str) {
      $this->subject= $this->contenttype= '';
      
      $t= strtok($str, "\n\r");
      while ($t) {
        if (("\t" != $t{0}) && (' ' != $t{0})) @list($k, $t)= explode(': ', $t, 2);
        
        switch (ucfirst($k)) {
          case HEADER_FROM:
            try {
              if ('' != trim($t)) {
                $this->setFrom(InternetAddress::fromString($t));
              }
            } catch (FormatException $e) {
              $this->setFrom(new InternetAddress(array(NULL, NULL), $t));
                            
              // Fall through
            }
            break;
            
          case HEADER_TO:
          case HEADER_CC:
          case HEADER_BCC:
            try {
              $k= strtolower($k);
              if ('' != trim($t)) {
                $this->addRecipient($k, InternetAddress::fromString($t));
              }
            } catch (FormatException $e) {
              $this->addRecipient($k, new InternetAddress(array(NULL, NULL), $t));
              
              // Fall through
            }
            break;
            
          case HEADER_MIMEVER:
            $this->mimever= $t;
            break;
            
          case HEADER_SUBJECT:
            $this->subject.= $t;
            break;
            
          case HEADER_CONTENTTYPE: 
            $this->contenttype.= $t;
            break;

          case HEADER_ENCODING: 
            $this->encoding= $t;
            break;
            
          case HEADER_DATE:
            $this->setDate($t);
            break;
          
          case HEADER_PRIORITY:
            $this->priority= (int)$t;
            break;

          case HEADER_MESSAGEID:
            $this->message_id= $t;
            break;
          
          default:
            $this->_setHeader($k, $t, "\n");
            break;
        }
        $t= strtok("\n\r");
      }
    }
    
    /**
     * Returns the header representation of the content-type. This includes
     * adding a charset information if one is set.
     *
     * @return  string header
     */
    protected function _getContenttypeHeaderString() {
      return $this->contenttype.(empty($this->charset) 
        ? '' 
        : ";\n\tcharset=\"".$this->charset.'"'
      );
    }

    /**
     * Build representation of address list
     *
     * @param   string t header token
     * @param   peer.mail.InternetAddress[] addrs
     * @return  string
     */
    protected function _astr($t, $addrs) {
      $l= '';
      for ($i= 0, $s= sizeof($addrs); $i < $s; $i++) {
        if (!$addrs[$i] instanceof InternetAddress) continue; // Ignore!
        $l.= $addrs[$i]->toString($this->getCharset()).",\n\t";
      }
      return empty($l) ? '' : $t.': '.substr($l, 0, -3)."\n";
    }
      
    /**
     * Check if a string needs to be encoded and encode it if necessary
     *
     * @param   string str
     * @return  string
     */
    protected function _qstr($str) {
      static $q;

      if (!isset($q)) $q= QuotedPrintable::getCharsToEncode();
      $n= FALSE;
      for ($i= 0, $s= strlen($str); $i < $s; $i++) {
        if (!in_array(ord($str{$i}), $q)) continue;
        $n= TRUE;
        break;
      }

      return $n ? QuotedPrintable::encode($str, $this->getCharset()) : $str;
    }
    
    /**
     * Return headers as string
     *
     * @return  string headers
     */
    public function getHeaderString() {
      static $priorities = array(
        MAIL_PRIORITY_LOW    => 'Low',
        MAIL_PRIORITY_NORMAL => 'Normal',
        MAIL_PRIORITY_HIGH   => 'High'
      );
      
      // Default headers
      $h= (
        $this->_astr(HEADER_FROM,   $a= array($this->from)).
        $this->_astr(HEADER_TO,     $this->to).
        $this->_astr(HEADER_CC,     $this->cc).
        $this->_astr(HEADER_BCC,    $this->bcc)
      );
      
      // Additional headers
      foreach (array_merge($this->headers, array(
        HEADER_SUBJECT      => $this->_qstr(@$this->subject),
        HEADER_CONTENTTYPE  => $this->_getContenttypeHeaderString(),
        HEADER_MIMEVER      => $this->mimever,
        HEADER_ENCODING     => $this->encoding,
        HEADER_PRIORITY     => $this->priority.' ('.$priorities[$this->priority].')',
        HEADER_DATE         => $this->date->toString('r'),
        HEADER_MESSAGEID    => $this->message_id,
        HEADER_RETURNPATH   => $this->returnpath
      )) as $key => $val) {
        if (!empty($val)) $h.= $key.': '.$val."\n";
      }
      return $h;
    }
  }
?>
