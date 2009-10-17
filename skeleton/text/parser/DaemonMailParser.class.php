<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.mail.Message',
    'text.parser.DaemonMessage',
    'text.parser.DaemonMailParserException',
    'text.parser.DaemonMailParserAutoresponderException'
  );
  
  /**
   * Mailer-Daemon failure notification parser
   *
   * <code>
   *   $p= new DaemonMailParser();                           
   *
   *   // Header handling                                     
   *   $p->addHeaderFound(                                    
   *     'X-Message-BackReference',                           
   *     'var_dump'                                           
   *   );                                                     
   *   $p->addHeaderMatch(                                    
   *     'X-Message-BackReference',                           
   *     '/^foo.bar@kk\.?(test)?([0-9]+)\.([0-9]+)$/',      
   *     'var_dump'                                           
   *   );                                                     
   *
   *   try {                                               
   *     $p->parse($message);                                 
   *   } catch(FormatException $e) {                  
   *                                                          
   *     // This does not seem to be a Mailer Daemon Message  
   *     $e->printStackTrace();                               
   *     exit(-1);                                            
   *   } catch(XPException $e) {                        
   *
   *     // Any other error                                   
   *     $e->printStackTrace();                               
   *     exit(-2);                                            
   *   }                                                      
   * </code> 
   *
   * @see      rfc://1893
   * @purpose  DaemonMail Parser
   */
  class DaemonMailParser extends Object {
    const
      DMP_SEARCH  = 0x0000,
      DMP_ORIGMSG = 0x0001,
      DMP_FINISH  = 0xFFFF;

    public
      $_hcb = array();
      
      
    /**
     * Set handler for the event that a header is found
     *
     * @param   string element
     * @param   function func
     */
    public function addHeaderFound($header, $func) {
      $this->_hcb[]= array($header, NULL, $func);
    }

    /**
     * Set handler for the event that a header matches a specified regular expression
     *
     * @param   string element
     * @param   string regex
     * @param   function func
     */
    public function addHeaderMatch($header, $regex, $func) {
      $this->_hcb[]= array($header, $regex, $func);
    }
    
    /**
     * Parses message/delivery-status body parts
     *
     * <pre>
     *   Reporting-MTA: dns; rly-xn01.mx.aol.com                     
     *   Arrival-Date: Mon, 17 Feb 2003 04:08:12 -0500 (EST)         
     *                                                               
     *   Final-Recipient: RFC822; foo.bar@aol.com                   
     *   Action: failed                                              
     *   Status: 2.0.0                                               
     *   Remote-MTA: DNS; air-xn02.mail.aol.com                      
     *   Diagnostic-Code: SMTP; 250 OK                               
     *   Last-Attempt-Date: Mon, 17 Feb 2003 04:08:27 -0500 (EST)    
     * </pre>
     *
     * @param   string str
     * @param   text.parser.DaemonMessage daemonmessage
     */
    protected function _parseDeliveryStatus($str, $daemonmessage) {
      $l= strtok(chop($str), "\n");
      $r= array();
      do {
        if ('' == chop($l)) continue;
        
        if (("\t" == $l{0}) || (' ' == $l{0})) {
          $r[ucfirst($k)].= $l;
          continue;
        }
        
        list($k, $v)= explode(': ', chop($l), 2);
        $r[ucfirst($k)]= $v;
      } while ($l= strtok("\n"));

      // Final-Recipient: rfc822; foo.bar@h2-systems.de
      // Final-Recipient: foo.bar@ihrhaus-massivbau.de
      if (isset($r['Final-Recipient'])) {
        list($type, $address)= explode(';', $r['Final-Recipient']);
        $daemonmessage->setFailedRecipient(InternetAddress::fromString(trim($address)));
      }

      // Reporting-MTA: dns;mailc0909.dte2k.de
      // Action: failed
      // Status: 5.1.1
      $daemonmessage->setReason($daemonmessage->getReason().sprintf(
        '%s [%s > %s: %s] ',
        @$r['Status'],
        @$r['Reporting-MTA'],
        @$r['Remote-MTA'],
        @$r['Action']
      ));

      // Diagnostic-Code: X-Postfix; host mail.epcnet.de[145.253.149.139] said: 554
      //     Error: too many hops (in reply to end of DATA command)      
      if (isset($r['Diagnostic-Code'])) {
        $daemonmessage->setReason($daemonmessage->getReason().$r['Diagnostic-Code']);
      }
      
      $daemonmessage->details= array_merge($daemonmessage->details, $r);
    }
    
    /**
     * Parse a stream
     *
     * @param   peer.mail.Message message
     * @return  bool success
     * @throws  lang.FormatException
     * @throws  lang.IllegalArgumentException
     */
    public function parse(Message $message) {
      static $magic= array(
        '550'                    => DAEMON_GENERIC,
        '5.5.0'                  => DAEMON_GENERIC,
        '4.4.1'                  => DAEMON_SMTPCONN,
        'Unknown local part'     => DAEMON_LOCALPART,
        'User unknown'           => DAEMON_LOCALPART,
        'unknown user'           => DAEMON_LOCALPART,
        'Quota'                  => DAEMON_QUOTA,
        'relaying'               => DAEMON_RELAYING,
        'Relay access denied'    => DAEMON_RELAYING,
        'no route to host'       => DAEMON_NOROUTE,
        'Cannot route'           => DAEMON_UNROUTEABLE,
        'unrouteable'            => DAEMON_UNROUTEABLE,
        'Delay'                  => DAEMON_DELAYED,
      );
      
      // First, look in the headers
      $state= self::DMP_SEARCH;
      
      // "In-Reply-To": These are stupid autoresponders or people replying 
      // to an address they shouldn't be.
      if (NULL !== ($irt= $message->getHeader('In-Reply-To'))) {
        throw new DaemonMailParserAutoresponderException(
          'Message has In-Reply-To header, Mailer Daemons do not set these [hint: Lame autoresponders do]',
          $message
        );
      }
      
      // Set up daemon mail object
      $daemonmessage= new DaemonMessage();
      $daemonmessage->setFrom($message->getFrom());
      $daemonmessage->date= $message->date;
      $daemonmessage->headers= $message->headers;
      $daemonmessage->subject= $message->subject;
      $daemonmessage->to= $message->to;
      
      // Is there a header named "X-Failed-Recipients"?
      if (NULL !== ($rcpt= $message->getHeader('X-Failed-Recipients'))) {
        $daemonmessage->setFailedRecipient(InternetAddress::fromString($rcpt));
      }
      
      // Check all hosts in received
      //
      // Received: from mxintern.kundenserver.de ([212.227.126.204])
      //         by mail.laudi.de with esmtp (Exim 2.10 #1)
      //         id 18jfZb-0006cv-00
      //         for foo.bar@parser.laudi.de; Fri, 14 Feb 2003 14:08:11 +0100
      // Received: from mail by mxintern.kundenserver.de with spam-scanned (Exim 3.35 #1)
      //         id 18jfZa-0003Ph-00
      //         for foo.bar@schlund.de; Fri, 14 Feb 2003 14:08:11 +0100
      // Received: from [194.73.242.6] (helo=wmpmta04-app.mail-store.com)
      //         by mxintern.kundenserver.de with esmtp (Exim 3.35 #1)
      //         id 18jfZa-0003Pe-00
      //         for foo.bar@kundenserver.de; Fri, 14 Feb 2003 14:08:10 +0100
      $t= strtok($message->getHeader('Received'), " \r\n\t([])=");
      do {
        if (
          (0 == strcasecmp('from', $t)) ||
          (0 == strcasecmp('by', $t)) ||
          (0 == strcasecmp('helo', $t))
        ) $host= strtok(" \r\n\t([])="); else continue;
        
        // Ignore: "from mail by xxx.yyy.zz"
        if (0 == strcasecmp('mail', $host) || !strstr($host, '.')) continue;
        
        $daemonmessage->details['Received'][]= $host;
      } while ($t= strtok(" \r\n\t([])=")); 
      
      // If this is a multipart message, try and seperate parts:
      //
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
      // foo.bar@aol.com>
      // 
      //    ----- Transcript of session follows -----
      // ... while talking to air-xn02.mail.aol.com.:
      // >>> RCPT foo.bar@aol.com>
      // <<< 550 MAILBOX NOT FOUND
      // 550 <foo.bar@aol.com>... User unknown
      // 
      // [-- Attachment #2 --]
      // [-- Type: message/delivery-status, Encoding: 7bit, Size: 0.3K --]
      // Content-Type: message/delivery-status
      // 
      // Reporting-MTA: dns; rly-xn01.mx.aol.com
      // Arrival-Date: Mon, 17 Feb 2003 04:08:12 -0500 (EST)
      // 
      // Final-Recipient: RFC822; foo.bar@aol.com
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
      //         for foo.bar@aol.com; Mon, 17 Feb 2003 10:08:11 +0100
      // Received: from [172.19.1.25] (helo=newsletter.kundenserver.de)
      //         by mxng09.kundenserver.de with esmtp (Exim 3.35 #1)
      //         id 18khFy-0003xv-00
      //         for foo.bar@aol.com; Mon, 17 Feb 2003 10:08:10 +0100
      // Received: from newsletter by newsletter.kundenserver.de with local (Exim 3.35 #1)
      //         id 18khDT-0000YH-00
      //         for foo.bar@aol.com; Mon, 17 Feb 2003 10:05:35 +0100
      // To: Herr Behrhof foo.bar@aol.com>
      // Subject: Wichtige Neuerung : Einf?hrung des 1&1 Kundenkennwortes f?r das 1&1 Control-Center
      // From: "1&1 Internet AG" foo.bar@1und1.com>
      // X-Priority: 3
      // Content-Type: text/plain; charset=iso-8859-1
      // Message-ID: foo.bar@newsletter.kundenserver.de>
      // X-News-BackReference: 12074.6170236
      // X-Ignore: yes
      // X-Binford: 61000 (more power)
      // MIME-Version: 1.0
      // Content-Transfer-Encoding: 8bit
      // Date: Mon, 17 Feb 2003 10:05:35 +0100
      if ($message instanceof MimeMessage) {
        $body= NULL;
        $daemonmessage->details['Daemon-Type']= DAEMON_TYPE_MULTIPART;
        
        while ($part= $message->getPart()) {
          
          switch (strtolower($part->getContentType())) {
            case 'message/delivery-status':
              $this->_parseDeliveryStatus(
                $part->getBody(),
                $daemonmessage
              );
              break;

            case 'message/rfc822':
              $state= self::DMP_ORIGMSG;
              $body= $part->parts[0]->getHeaderString()."\r\n";
              break;
              
            case 'text/rfc822-headers':
              $state= self::DMP_ORIGMSG;
              $body= $part->getBody();
              break;
              

            case 'text/plain':
              // ------_=_NextPart_000_01C2D43F.51E7081E
              // Content-Type: text/plain;
              //         charset="iso-8859-1"
              // 
              // Your message
              // 
              //   To:      Foo Bar
              //   Subject: Cancellation order on 14.02.2003
              //   Sent:    Fri, 14 Feb 2003 15:24:24 -0000
              // 
              // did not reach the following recipient(s):
              // 
              // foo.bar@NTL.com on Fri, 14 Feb 2003 15:39:49 -0000
              //     The recipient name is not recognized
              //         The MTS-ID of the original message is: c=us;a= ;p=ntl
              // corp;l=MAST-DC0-SE080302141539XM8FHXVY
              //     MSEXCH:IMS:NTL Corp:CableTel Datacentre:MAST-DC0-SE08 0 (000C05A6)
              // Unknown Recipient
              if (strstr($part->getBody(), 'MSEXCH')) {
                $l= strtok($part->getBody(), "\n");
                
                // Find the first line with an @ in it
                $r= FALSE;
                do {
                  if (strstr($l, '@')) $r= TRUE;
                  $r && $daemonmessage->setReason(trim($daemonmessage->getReason()).' '.trim($l));
                  
                } while ($l= strtok("\n"));
                
                // Done
                break;
              }

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
              // <foo.bar@crosswinds.net>: host 127.0.0.1[127.0.0.1] said: 550 5.1.0
              //     foo.bar@crosswinds.net>: User unknown (in reply to end of DATA
              //     command)
              if (strstr($part->getBody(), 'Postfix')) {
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
                
                // Done
                break;
              }
              
              break;

            default:
              // Ignore...
          }
        }
      } else {
        $body= $message->getBody();
      }
      
      // Begin tokenizing
      if (!($t= strtok($body, "\n"))) {
        trigger_error('Body: '.var_export($body, 1), E_USER_NOTICE);
        throw new FormatException('Tokenizing failed');
      }
      
      // Loop through tokens
      $v= '';
      do {
        switch ($state) {
          case self::DMP_ORIGMSG:
            if (
              ('' == chop($t)) || 
              (!strstr($t, ': ') && "\t" != $t{0})
            ) {
              $state= self::DMP_FINISH;
              break;
            }
            
            if ("\t" == $t{0}) {
              $v.= ' '.chop($t);
            } else {
              list($k, $v)= explode(':', $t, 2);
            }
            
            if (empty($k)) continue;
            foreach ($this->_hcb as $defines) {
              if (0 != strcasecmp($k, $defines[0])) continue;
              
              $regs= array();
              if (
                (NULL == $defines[1]) ||
                (preg_match($defines[1], $v, $regs))
              ) {
                call_user_func_array($defines[2], array($daemonmessage, $v, $regs));
              }
            }
            break;
            
          case self::DMP_SEARCH:
            // Sendmail
            //
            //    ----- The following addresses had permanent fatal errors -----
            // foo.bar@aol.com>
            // 
            //    ----- Transcript of session follows -----
            // ... while talking to mx01.kundenserver.de.:
            // >>> RCPT <foo.bar@bilgin-online.de>
            // <<< 550 Cannot route to foo.bar@bilgin-online.de>
            // 550 5.1.1 Avni Bilgin foo.bar@bilgin-online.de>... User unknown
            if ('   ----- The following addresses' == substr($t, 0, 32)) {
              $daemonmessage->details['Daemon-Type']= DAEMON_TYPE_SENDMAIL;
              
              // The next line contains the recipient
              $daemonmessage->setFailedRecipient(InternetAddress::fromString(strtok("\n")));
              
              // Swallow one line
              strtok("\n");
              
              // Read until next empty line
              do {
                if ('' == chop($t)) break;
                $daemonmessage->setReason(trim($daemonmessage->getReason()).' '.trim($t));
                
              } while ($t= strtok("\n"));
  
              $state= self::DMP_ORIGMSG;            
              continue;
            }

            // Exim1
            // 
            // This message was created automatically by mail delivery software (Exim).
            // A message that you sent could not be delivered to one or more of its
            // recipients. This is a permanent error. The following address(es) failed:
            //   foo.bar@b-w-f.net
            //     SMTP error from remote mailer after RCPT foo.bar@b-w-f.net>:
            //     host mx01.kundenserver.de [212.227.126.152]: 550 Cannot route to foo.bar@b-w-f.net>
            // ------ This is a copy of the message, including all the headers. ------
            //
            // Exim2
            // 
            // This message was created automatically by mail delivery software (Exim).
            //   
            //   A message that you sent has not yet been delivered to one or more of its
            //   recipients after more than 48 hours on the queue on togal.kundenserver.de.
            //   
            //   The message identifier is:     18ixoL-0003hN-00
            //   The subject of the message is: =?iso-8859-1?q?Domain=FCbertragung?=
            //   The date of the message is:    Wed, 12 Feb 2003 15:24:29 +0100
            //   
            //   The address to which the message has not yet been delivered is:
            //   
            //     foo.bar@wwlms.de
            //       Delay reason: SMTP error from remote mailer after RCPT TO:<foo.bar@wwlms.de>:
            //       host mx01.kundenserver.de [212.227.126.211]: 451 Cannot check <foo.bar@wwlms.de> at this time - please try later
            //   
            //   No action is required on your part. Delivery attempts will continue for
            //   some time, and this warning may be repeated at intervals if the message
            //   remains undelivered. Eventually the mail delivery software will give up,
            //   and when that happens, the message will be returned to you.
            if ('This message was created automatically by mail delivery software (Exim).' == substr($t, 0, 72)) {
              $daemonmessage->details['Daemon-Type']= DAEMON_TYPE_EXIM;
              
              $state= self::DMP_FINISH;

              // Find indented lines until ----- appears
              $c= FALSE;
              do {
                if ('-----' == substr($t, 0, 5)) $c= TRUE;
                if ($c && ('' == chop($t))) break;
                
                if (strstr($t, 'copy of the message')) $state= self::DMP_ORIGMSG;
                if ('  ' != substr($t, 0, 2)) continue;
                
                // Parse out host/IP
                if (preg_match('#host ([^ ]+) \[([^ ]+)\]:#', $t, $regs)) {
                  $daemonmessage->details['Reporting-MTA']= 'dns; '.$regs[1];
                }

                $daemonmessage->setReason(trim($daemonmessage->getReason()).' '.trim($t));
              } while ($t= strtok("\n"));

              // Now, work on original message (swallowing one line)
              $t= strtok("\n");
              continue;
            }

            // T-Online
            // 
            // |------------------------- Failed addresses follow: ---------------------|
            // foo.bar@t-online.de> ... unknown user / Teilnehmer existiert nicht
            // |------------------------- Message text follows: ------------------------|
            if ('|------------------------- Failed addresses follow:' == substr($t, 0, 51)) {
              $daemonmessage->details['Daemon-Type']= DAEMON_TYPE_TONLINE;
              $daemonmessage->details['Reporting-MTA']= 'dns; '.$daemonmessage->details['Received'][sizeof($daemonmessage->details['Received'])- 1];
              $daemonmessage->details['Remote-MTA']= 'dns; '.$daemonmessage->details['Received'][sizeof($daemonmessage->details['Received'])- 2];
              
              $daemonmessage->setReason(trim(strtok("\n")));
              strtok("\n");

              // Now, work on original message
              $state= self::DMP_ORIGMSG;
              continue;
            }

            // Postfix
            // 
            // Reporting-MTA: dns; cia.schlund.de
            // Arrival-Date: Sun, 12 May 2002 09:06:07 +0200
            // 
            // Final-Recipient: RFC822; foo.bar@bilgin-online.de
            // Action: failed
            // Status: 5.1.1
            // Remote-MTA: DNS; mx01.kundenserver.de
            // Diagnostic-Code: SMTP; 550 Cannot route to foo.bar@bilgin-online.de>
            // Last-Attempt-Date: Sun, 12 May 2002 09:34:05 +0200
            if ('Reporting-MTA: ' == substr($t, 0, 15)) {
              $daemonmessage->details['Daemon-Type']= DAEMON_TYPE_POSTFIX;
              
              $str= '';
              $c= 0;
              do {
                if ('' == chop($t)) $c++;
                $str.= $t."\n";
              } while ($t= strtok("\n") && $c < 2);
              
              $this->_parseDeliveryStatus($str, $daemonmessage);

              // Now, work on original message
              $state= self::DMP_ORIGMSG;              
              continue;
            }
            
            // Exim
            // 
            // A message that you sent contained one or more recipient addresses that were
            // incorrectly constructed:
            // 
            //   =?iso-8859-1?Q?Herr_Foo?= <1234-986@foo.bar;Bjoern.Foo@foo.bar>: malformed address: ;Bjoern.Foo@foo.bar> may not follow =?iso-8859-1?Q?Herr_Foo?= <1234-986@foo.bar
            // 
            // This address has been ignored. There were no other addresses in your
            // message, and so no attempt at delivery was possible.
            // 
            // ------ This is a copy of your message, including all the headers.
            // ------ No more than 100K characters of the body are included.
            if ('A message that you sent contained one or more recipient addresses' == substr($t, 0, 65)) {
              $daemonmessage->details['Daemon-Type']= DAEMON_TYPE_EXIM;
              
              // Find first empty line after ----
              $c= FALSE;
              do {
                if ('-----' == substr($t, 0, 5)) $c= TRUE;
                if ($c && ('' == chop($t))) break;
                
                $c || $daemonmessage->setReason(trim($daemonmessage->getReason()).' '.trim($t));
              } while ($t= strtok("\n"));
              
              // Now, work on original message
              $state= self::DMP_ORIGMSG;
              continue;
            }
            
            // QMail
            // 
            // Hi. This is the qmail-send program at mailje.nl.
            // I'm afraid I wasn't able to deliver your message to the following addresses.
            // This is a permanent error; I've given up. Sorry it didn't work out.
            // 
            // <foo.bar@airforce.net>:
            // Sorry, no mailbox here by that name.
            // 
            // --- Below this line is a copy of the message.
            if ('This is the qmail-send program' == substr($t, 4, 30)) {
              $daemonmessage->details['Daemon-Type']= DAEMON_TYPE_QMAIL;
              
              // Find first line starting with <
              do { 
                if ('<' == $t{0}) break;
              } while ($t= strtok("\n"));
              
              $daemonmessage->setFailedRecipient(InternetAddress::fromString(substr(chop($t), 0, -1)));
              
              // Find line beginning with ---
              do { 
                if ('---' == substr($t, 0, 3)) break;
                $daemonmessage->setReason(trim($daemonmessage->getReason()).' '.trim($t));
              } while ($t= strtok("\n"));
              
              // If the reason (trimmed) contains no spaces it's probably something like this:
              // - Mailbox_quota_exceeded_-_Mailbox_voll/
              // - foo.bar@alloncd.de>/Giving_up_on_212.227.126.159./
              if (!strstr(trim($daemonmessage->reason), ' ')) {
                $daemonmessage->setReason(str_replace('_', ' ', $daemonmessage->reason));
              }
              
              // Now, work on original message
              $state= self::DMP_ORIGMSG;
              continue;
            }
            
            // Nemesis? (Schlund+Partner mail system)
            //
            // This message was created automatically by mail delivery software.
            // 
            // A message that you sent could not be delivered to one or more of
            // its recipients. The following addresses failed:
            // 
            //   <info@example.com>
            // 
            // SMTP error from remote server after RCPT command:
            // host mx00.1and1.com[217.222.222.222]:
            // 550 <info@example.com>: invalid address
            // 
            // 
            // --- The header of the original message is following. ---
            if (0 == strncmp('This message was created automatically by mail delivery software.', $t, 65)) {
              $daemonmessage->details['Daemon-Type']= DAEMON_TYPE_EXIM;

              // Find first line starting with with an email address
              do {
                if (FALSE === $t) { throw new DaemonMailParserException('Cannot parse message', $message); }
                if (FALSE !== strpos($t, '@')) break;
              } while ($t= strtok("\n"));
              
              $daemonmessage->setFailedRecipient(InternetAddress::fromString(trim($t, "\n\r<>: ")));
              
              // Next line
              $t= strtok("\n");

              // Read until next empty line, the last one contains the error message
              do {
                if ('' == trim($t)) break;
                $daemonmessage->setReason($daemonmessage->getReason()."\n".trim($t));
              } while ($t= strtok("\n"));

              // Read until "--- The header of the ..." or an equivalent
              while (
                0 != strncmp('--- The header of the original message is following. ---', $t, 56) &&
                0 != strncmp('------ This is a copy of the message, including all the headers. ------', $t, 71) &&
                0 != strncmp('-----------------------------------------------------------------', $t, 65)
              ) {
                if (FALSE === $t) { throw new DaemonMailParserException('Cannot parse message', $message); }
                $t= strtok("\n");
              }
              
              // Swallow next two lines
              $t= strtok("\n");
              $t= strtok("\n");
              
              $state= self::DMP_ORIGMSG;
              continue;
            }
            
            
            break;
          
          case self::DMP_FINISH:
            break 2;
          
          default: 
            throw new DaemonMailParserException('Unknown state '.var_export($state, 1), $message);
        }
        
      } while ($t= strtok("\n"));
      
      // No reason found?
      if (self::DMP_FINISH != $state) {
        trigger_error('Headers: '.var_export($message->headers, 1), E_USER_ERROR);
        trigger_error('Body: '.($message instanceof MimeMessage 
          ? sizeof($message->parts).' parts'
          : strlen($message->body).' bytes'
        ), E_USER_ERROR);
        
        $states= array(
          self::DMP_SEARCH    => 'self::DMP_SEARCH',
          self::DMP_ORIGMSG   => 'self::DMP_ORIGMSG',
          self::DMP_FINISH    => 'self::DMP_FINISH'
        );
        throw new FormatException('Unable to parse message, state "'.$states[$state].'"');
      }
      
      // Apply some string magic on the reason
      foreach ($magic as $k => $v) {
        if (stristr($daemonmessage->reason, $k)) $daemonmessage->status= $v;
      }
      
      return $daemonmessage;
    }
  }
?>
