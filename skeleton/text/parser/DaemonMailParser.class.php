<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.mail.Message');

  /**
   * Mailer-Daemon failure notification parser
   *
   * <code>
   *   $p= &new DaemonMailParser();                           
   *
   *   // Header handling                                     
   *   $p->addHeaderFound(                                    
   *     'X-Message-BackReference',                           
   *     $f= 'var_dump'                                           
   *   );                                                     
   *   $p->addHeaderMatch(                                    
   *     'X-Message-BackReference',                           
   *     '/^([0-9]+)@@kk\.?(test)?([0-9]+)\.([0-9]+)$/',      
   *     $f= 'var_dump'                                           
   *   );                                                     
   *
   *   try(); {                                               
   *     $p->parse($message);                                 
   *   } if (catch('FormatException', $e)) {                  
   *                                                          
   *     // This does not seem to be a Mailer Daemon Message  
   *     $e->printStackTrace();                               
   *     exit(-1);                                            
   *   } if (catch('Exception', $e)) {                        
   *
   *     // Any other error                                   
   *     $e->printStackTrace();                               
   *     exit(-2);                                            
   *   }                                                      
   * </code> 
   *
   * @purpose  DaemonMail Parser
   */
  class DaemonMailParser extends Object {
    var
      $_hcb = array();
      
      
    /**
     * Set handler for the event that a header is found
     *
     * @access  public
     * @param   string element
     * @param   &function func
     */
    function addHeaderFound($header, &$func) {
      $this->_hcb['found_'.$header]= array(NULL, &$func);
    }

    /**
     * Set handler for the event that a header matches a specified regular expression
     *
     * @access  public
     * @param   string element
     * @param   string regex
     * @param   &function func
     */
    function addHeaderMatch($header, $regex, &$func) {
      $this->_hcb['match_'.$header]= array($regex, &$func);
    }
    
    /**
     * Parse a stream
     *
     * @access  public 
     * @param   &peer.mail.Message
     * @return  bool success
     * @throws  FormatException
     * @throws  IllegalArgumentException
     */
    function parse(&$message) {
      if (!is_a($message, 'Message')) {
        trigger_error('Type: '.get_class($message), E_USER_NOTICE);
        return throw(new IllegalArgumentException('Parameter message is not peer.mail.Message object'));
      }
      
      var_dump('FROM', $message->getFrom());
      var_dump('DATE', $message->date->toString());
      
      // First, look in the headers
      
      // Is there a header named "X-Failed-Recipients"?
      if (NULL !== ($rcpt= $message->getHeader('X-Failed-Recipients'))) {
        var_dump('FAILED_RECIPIENT', $rcpt);
      }
      
      // "In-Reply-To": These are stupid autoresponders or people replying 
      // to an address they shouldn't be.
      if (NULL !== ($irt= $message->getHeader('In-Reply-To'))) {
        trigger_error('Message is in reply to: '.$irt, E_USER_NOTICE);
        return throw(new FormatException('Message has In-Reply-To header, Mailer Daemons do not set these'));
      }
      
      $t= strtok($message->getBody(), "\r\n");
      do {
        # printf(">> %s\n", $t);
        
        // Sendmail
        // ========
        // ... while talking to mx01.kundenserver.de.:
        // >>> RCPT To:<avni@bilgin-online.de>
        // <<< 550 Cannot route to <avni@bilgin-online.de>
        // 550 5.1.1 Avni Bilgin <avni@bilgin-online.de>... User unknown
        if ('... while talking to' == substr($t, 0, 20)) {
          var_dump('SENDMAIL', $message->headers, $t);
          
          // Read six lines
          
          $state= DMP_HEADERS;
          continue;
        }

        // Exim
        // ====
        // This message was created automatically by mail delivery software (Exim).
        // A message that you sent could not be delivered to one or more of its
        // recipients. This is a permanent error. The following address(es) failed:
        //   webmaster@b-w-f.net
        //     SMTP error from remote mailer after RCPT TO:<webmaster@b-w-f.net>:
        //     host mx01.kundenserver.de [212.227.126.152]: 550 Cannot route to <webmaster@b-w-f.net>
        // ------ This is a copy of the message, including all the headers. ------
        if ('This message was created automatically by mail delivery software' == substr($t, 0, 64)) {
          var_dump('EXIM', $message->headers, $t);
          
          // Read six lines
          
          $state= DMP_HEADERS;
          continue;
        }
        
        // T-Online
        // ========
        // |------------------------- Failed addresses follow: ---------------------|
        // <roland.tusche.@t-online.de> ... unknown user / Teilnehmer existiert nicht
        // |------------------------- Message text follows: ------------------------|
        if ('|------------------------- Failed addresses follow:' == substr($t, 0, 51)) {
          var_dump('T-ONLINE', $message->headers, $t);
          
          // Read two lines
          
          $state= DMP_HEADERS;
          continue;
        }
        
        // Postfix
        // =======
        // Reporting-MTA: dns; cia.schlund.de
        // Arrival-Date: Sun, 12 May 2002 09:06:07 +0200
        // 
        // Final-Recipient: RFC822; avni@bilgin-online.de
        // Action: failed
        // Status: 5.1.1
        // Remote-MTA: DNS; mx01.kundenserver.de
        // Diagnostic-Code: SMTP; 550 Cannot route to <avni@bilgin-online.de>
        // Last-Attempt-Date: Sun, 12 May 2002 09:34:05 +0200
        if ('Final-Recipient: RFC822; ' == substr($t, 0, 25)) {
          var_dump('POSTFIX', $message->headers, $t);
          
          $state= DMP_HEADERS;
          continue;
        }
        
      } while ($t= strtok("\r\n"));
      
      if ($state != DMP_HEADERS) {
        echo "######################################################################################\n";
        var_dump($message->headers, $message->getBody());
      }
    }
  }
?>
