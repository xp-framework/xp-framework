<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.mail.Message', 'text.parser.DaemonMessage');
  
  define('DMP_SEARCH',   0x0000);
  define('DMP_ORIGMSG',  0x0001);
  define('DMP_FINISH',   0xFFFF);

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
      $this->_hcb[]= array($header, NULL, &$func);
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
      $this->_hcb[]= array($header, $regex, &$func);
    }
    
    /**
     * Parses message/delivery-status body parts
     *
     * <pre>
     *   Reporting-MTA: dns; rly-xn01.mx.aol.com                     
     *   Arrival-Date: Mon, 17 Feb 2003 04:08:12 -0500 (EST)         
     *                                                               
     *   Final-Recipient: RFC822; uliruedi@aol.com                   
     *   Action: failed                                              
     *   Status: 2.0.0                                               
     *   Remote-MTA: DNS; air-xn02.mail.aol.com                      
     *   Diagnostic-Code: SMTP; 250 OK                               
     *   Last-Attempt-Date: Mon, 17 Feb 2003 04:08:27 -0500 (EST)    
     * </pre>
     *
     * @access  private
     * @param   string str
     * @param   &text.parser.DaemonMessage daemonmessage
     */
    function _parseDeliveryStatus($str, &$daemonmessage) {
      $l= strtok(chop($str), "\n");
      $r= array();
      do {
        if ('' == chop($l)) continue;
        
        if ("\t" == $l{0}) {
          $r[ucfirst($k)].= $l;
          continue;
        }
        
        list($k, $v)= explode(': ', chop($l), 2);
        $r[ucfirst($k)]= $v;
      } while ($l= strtok("\n"));

      // Final-Recipient: rfc822; info@h2-systems.de
      // Final-Recipient: rfc822;webmaster@ihrhaus-massivbau.de
      if (isset($r['Final-Recipient'])) {
        list($type, $address)= explode(';', $r['Final-Recipient']);
        $daemonmessage->setFailedRecipient(InternetAddress::fromString(trim($address)));
      }

      // Reporting-MTA: dns;mailc0909.dte2k.de
      // Action: failed
      // Status: 5.1.1
      $daemonmessage->setReason($daemonmessage->getReason().sprintf(
        '%s [%s: %s] > ',
        @$r['Status'],
        @$r['Reporting-MTA'],
        @$r['Action']
      ));

      // Diagnostic-Code: X-Postfix; host mail.epcnet.de[145.253.149.139] said: 554
      //     Error: too many hops (in reply to end of DATA command)      
      if (isset($r['Diagnostic-Code'])) {
        $daemonmessage->setReason($daemonmessage->getReason().$r['Diagnostic-Code']);
      }
      
      # var_dump('MESSAGE/DELIVERY', $r);
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
      static $magic= array(
        '550'   => DAEMON_GENERIC,
        '5.5.0' => DAEMON_GENERIC,
        '4.4.1' => DAEMON_SMTPCONN,
        'Unknown local part' => DAEMON_LOCALPART,
        'User unknown' => DAEMON_LOCALPART,
        'Quota' => DAEMON_QUOTA,
        'relaying' => DAEMON_RELAYING,
        'no route to host' => DAEMON_NOROUTE,
        'Cannot route' => DAEMON_NOROUTE,
      );
      
      if (!is_a($message, 'Message')) {
        trigger_error('Type: '.get_class($message), E_USER_NOTICE);
        return throw(new IllegalArgumentException('Parameter message is not peer.mail.Message object'));
      }
      
      // First, look in the headers
      $state= DMP_SEARCH;
      
      // "In-Reply-To": These are stupid autoresponders or people replying 
      // to an address they shouldn't be.
      if (NULL !== ($irt= $message->getHeader('In-Reply-To'))) {
        trigger_error('Message is in reply to: '.$irt, E_USER_NOTICE);
        return throw(new FormatException('Message has In-Reply-To header, Mailer Daemons do not set these'));
      }
      
      // Set up daemon mail object
      $daemonmessage= &new DaemonMessage();
      $daemonmessage->setFrom($message->getFrom());
      $daemonmessage->date= &$message->date;
      $daemonmessage->headers= &$message->headers;
      
      // Is there a header named "X-Failed-Recipients"?
      if (NULL !== ($rcpt= $message->getHeader('X-Failed-Recipients'))) {
        $daemonmessage->setFailedRecipient(InternetAddress::fromString($rcpt));
      }
      
      // If this is a multipart message, try and seperate parts:
      // =======================================================
      // [-- Attachment #1 --]
      // [-- Type: text/plain, Encoding: 7bit, Size: 1.0K --]
      // 
      // The original message was received at Mon, 17 Feb 2003 04:08:12 -0500 (EST)
      // from moutng.kundenserver.de [212.227.126.189]
      // 
      // 
      // *** ATTENTION ***
      // 
      // Your e-mail is being returned to you because there was a problem with its
      // delivery.  The address which was undeliverable is listed in the section
      // labeled: "----- The following addresses had permanent fatal errors -----".
      // 
      // The reason your mail is being returned to you is listed in the section
      // labeled: "----- Transcript of Session Follows -----".
      // 
      // The line beginning with "<<<" describes the specific reason your e-mail could
      // not be delivered.  The next line contains a second error message which is a
      // general translation for other e-mail servers.
      // 
      // Please direct further questions regarding this message to your e-mail
      // administrator.
      // 
      // --AOL Postmaster
      // 
      // 
      // 
      //    ----- The following addresses had permanent fatal errors -----
      // <uliruedi@aol.com>
      // 
      //    ----- Transcript of session follows -----
      // ... while talking to air-xn02.mail.aol.com.:
      // >>> RCPT To:<uliruedi@aol.com>
      // <<< 550 MAILBOX NOT FOUND
      // 550 <uliruedi@aol.com>... User unknown
      // 
      // [-- Attachment #2 --]
      // [-- Type: message/delivery-status, Encoding: 7bit, Size: 0.3K --]
      // Content-Type: message/delivery-status
      // 
      // Reporting-MTA: dns; rly-xn01.mx.aol.com
      // Arrival-Date: Mon, 17 Feb 2003 04:08:12 -0500 (EST)
      // 
      // Final-Recipient: RFC822; uliruedi@aol.com
      // Action: failed
      // Status: 2.0.0
      // Remote-MTA: DNS; air-xn02.mail.aol.com
      // Diagnostic-Code: SMTP; 250 OK
      // Last-Attempt-Date: Mon, 17 Feb 2003 04:08:27 -0500 (EST)
      // 
      // [-- Attachment #3 --]
      // [-- Type: message/rfc822, Encoding: 7bit, Size: 4.1K --]
      // Content-Type: message/rfc822
      // 
      // Received: from  moutng.kundenserver.de (moutng.kundenserver.de [212.227.126.189]) by
      // +rly-xn01.mx.aol.com (v90_r2.6) with ESMTP id MAILRELAYINXN19-0217040812; Mon, 17 Feb 2003
      // +04:08:12 -0500
      // Received: from [212.227.126.159] (helo=mxng09.kundenserver.de)
      //         by moutng.kundenserver.de with esmtp (Exim 3.35 #1)
      //         id 18khFz-0000pQ-00
      //         for UliRuedi@aol.com; Mon, 17 Feb 2003 10:08:11 +0100
      // Received: from [172.19.1.25] (helo=newsletter.kundenserver.de)
      //         by mxng09.kundenserver.de with esmtp (Exim 3.35 #1)
      //         id 18khFy-0003xv-00
      //         for UliRuedi@aol.com; Mon, 17 Feb 2003 10:08:10 +0100
      // Received: from newsletter by newsletter.kundenserver.de with local (Exim 3.35 #1)
      //         id 18khDT-0000YH-00
      //         for UliRuedi@aol.com; Mon, 17 Feb 2003 10:05:35 +0100
      // To: Herr Behrhof <UliRuedi@aol.com>
      // Subject: Wichtige Neuerung : Einf?hrung des 1&1 Kundenkennwortes f?r das 1&1 Control-Center
      // From: "1&1 Internet AG" <noreply@1und1.com>
      // X-Priority: 3
      // Content-Type: text/plain; charset=iso-8859-1
      // Message-ID: <NL12074.2946803@newsletter.kundenserver.de>
      // X-News-BackReference: 12074.6170236
      // X-Ignore: yes
      // X-Binford: 61000 (more power)
      // MIME-Version: 1.0
      // Content-Transfer-Encoding: 8bit
      // Date: Mon, 17 Feb 2003 10:05:35 +0100
      if (is_a($message, 'MimeMessage')) {
        $body= NULL;
        while ($part= &$message->getPart()) {
          if (MIME_DISPOSITION_INLINE != $part->getDisposition()) {
          
            // Ignore attachments
            continue;
          }
          
          # printf(">> PART >> %s\n", var_export($part, 1));
          
          switch (strtolower($part->getContentType())) {
            case 'message/delivery-status':
              # printf("DELIVERY>> %s\n", var_export($part, 1));
              
              $this->_parseDeliveryStatus(
                $part->getBody(),
                $daemonmessage
              );
              
              # var_dump($daemonmessage);
              
              break;

            case 'message/rfc822':
              # printf("RFC822  >> %s\n", var_export($part, 1));

              $state= DMP_ORIGMSG;
              $body= $part->parts[0]->getHeaderString();
              break;
              
            case 'text/rfc822-headers':
              # printf("RFC822HD>> %s\n", var_export($part, 1));
              
              $state= DMP_ORIGMSG;
              $body= $part->getBody();
              break;
              

            case 'text/plain':
              // ------=_Part_10455873563e52659c52a535.44444108
              // Content-Type: text/plain; charset="iso-8859-1"
              // Content-Transfer-Encoding: US-ASCII
              // Content-ID: <1>
              // Content-Disposition: inline
              // 
              // This is the Postfix program at host mx5.crosswinds.net.
              // 
              // I'm sorry to have to inform you that the message returned
              // below could not be delivered to one or more destinations.
              // 
              // For further assistance, please send mail to <postmaster>
              // 
              // If you do so, please include this problem report. You can
              // delete your own text from the message returned below.
              // 
              //                         The Postfix program
              // 
              // <andreasreichel@crosswinds.net>: host 127.0.0.1[127.0.0.1] said: 550 5.1.0
              //     <andreasreichel@crosswinds.net>: User unknown (in reply to end of DATA
              //     command)
              $l= strtok($part->getBody(), "\n");

              // Find "The Postfix program"
              do {
                if ('The Postfix program' != trim(chop($l))) continue;

                // Swallow one line
                strtok("\n");

                // Fetch mail address
                $daemonmessage->setFailedRecipient(InternetAddress::fromString(strtok('<>')));

                // Append until first empty line is found
                while ($l= strtok("\n")) {
                  if ('' == chop($l)) break;
                  $daemonmessage->setReason(trim($daemonmessage->getReason()).' '.trim($l));
                }

              } while ($l= strtok("\n"));
              break;

            default:
              # var_dump('###IGNORE >>'.$part->getContentType().'<< IGNORE###');
              
              // Ignore...
          }
        }
      } else {
        $body= $message->getBody();
      }
      
      // Begin tokenizing
      if (!($t= strtok($body, "\n"))) {
        trigger_error('Body: '.var_export($body, 1), E_USER_NOTICE);
        return throw(new FormatException('Tokenizing failed'));
      }
      
      // Loop through tokens
      do {
        switch ($state) {
          case DMP_ORIGMSG:
            # printf("ORIGMSG >> %s\n", $t);
            if (
              ('' == chop($t)) || 
              (!strstr($t, ': ') && "\t" != $t{0})
            ) {
              $state= DMP_FINISH;
              break;
            }   
            
            if ("\t" == $t{0}) {
              $v.= ' '.chop($t);
            } else {
              list($k, $v)= explode(': ', chop($t), 2);
            }
            
            foreach ($this->_hcb as $defines) {
              if (0 != strcasecmp($k, $defines[0])) continue;
              
              $regs= array();
              if (
                (NULL == $defines[1]) ||
                (preg_match($defines[1], $v, $regs))
              ) {
                # printf("CALLBACK>> %s(%s %s)\n", var_export($defines[2], 1), var_export($v, 1), var_export($regs, 1));
                call_user_func($defines[2], $v, $regs);
              }
            }
            break;
            
          case DMP_SEARCH:
            # printf("SEARCH  >> %s\n", $t);
            
            // Sendmail
            // ========
            //    ----- The following addresses had permanent fatal errors -----
            // <friwe@aol.com>
            // 
            //    ----- Transcript of session follows -----
            // ... while talking to mx01.kundenserver.de.:
            // >>> RCPT To:<avni@bilgin-online.de>
            // <<< 550 Cannot route to <avni@bilgin-online.de>
            // 550 5.1.1 Avni Bilgin <avni@bilgin-online.de>... User unknown
            if ('   ----- The following addresses' == substr($t, 0, 32)) {
              # var_dump('SENDMAIL', $message->headers, $t);

              // Read six lines
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

              // Find indented lines until -- appears
              do {
                if ('--' == substr($t, 0, 2)) break;
                if ('  ' != substr($t, 0, 2)) continue;

                $daemonmessage->setReason(trim($daemonmessage->getReason()).' '.trim($t));
              } while ($t= strtok("\n"));

              // Now, work on original message (swallowing one line)
              $t= strtok("\n");
              $state= DMP_ORIGMSG;
              continue;
            }

            // T-Online
            // ========
            // |------------------------- Failed addresses follow: ---------------------|
            // <roland.tusche.@t-online.de> ... unknown user / Teilnehmer existiert nicht
            // |------------------------- Message text follows: ------------------------|
            if ('|------------------------- Failed addresses follow:' == substr($t, 0, 51)) {

              $daemonmessage->setReason(trim(strtok("\n")));
              strtok("\n");

              // Now, work on original message
              $state= DMP_ORIGMSG;
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
            if ('Reporting-MTA: ' == substr($t, 0, 15)) {
              $str= '';
              $c= 0;
              do {
                if ('' == chop($t)) $c++;
                $str.= $t."\n";
              } while ($t= strtok("\n") && $c < 2);
              
              $this->_parseDeliveryStatus($str, $daemonmessage);

              // Now, work on original message
              $state= DMP_ORIGMSG;              
              continue;
            }
            
            // QMail
            // =====
            // Hi. This is the qmail-send program at mailje.nl.
            // I'm afraid I wasn't able to deliver your message to the following addresses.
            // This is a permanent error; I've given up. Sorry it didn't work out.
            // 
            // <sniper@airforce.net>:
            // Sorry, no mailbox here by that name.
            // 
            // --- Below this line is a copy of the message.
            if ('This is the qmail-send program' == substr($t, 4, 30)) {
              # var_dump('QMAIL', $message->headers, $t);
              
              // Find first empty line
              do { 
                if ('' == chop($t)) break;
              } while ($t= strtok("\n"));
              
              $daemonmessage->setFailedRecipient(InternetAddress::fromString(substr(chop(strtok("\n")), 0, -1)));
              
              // Find line beginning with ---
              do { 
                if ('---' == substr($t, 0, 3)) break;
                $daemonmessage->setReason(trim($daemonmessage->getReason()).' '.trim($t));
              } while ($t= strtok("\n"));
              
              // Now, work on original message
              $state= DMP_ORIGMSG;
              continue;
            }
            
            break;
          
          case DMP_FINISH:
            break 2;
          
          default: 
            return throw(new FormatException('Unknown state '.var_export($state, 1)));
        }
        
      } while ($t= strtok("\n"));
      
      if (empty($daemonmessage->reason)) {
        trigger_error('Headers: '.var_export($message->headers, 1), E_USER_ERROR);
        trigger_error('Body: '.(is_a($message, 'MimeMessage') 
          ? sizeof($message->parts).' parts'
          : strlen($message->body).' bytes'
        ), E_USER_ERROR);
        return throw(new FormatException('Unable to parse message'));
      }
      
      // Apply some string magic on the reason
      # printf("[MAGIC] %s\n", $daemonmessage->reason);
      foreach ($magic as $k => $v) {
        # printf("[MAGIC] ? %s: %s\n", $k, var_export((bool)stristr($daemonmessage->reason, $k), 1));
        if (stristr($daemonmessage->reason, $k)) $daemonmessage->status= $v;
      }
      
      return $daemonmessage;
    }
  }
?>
