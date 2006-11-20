<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.mail.Message', 'org.bugzilla.BugConstants');

  /**
   * Message to be sent to bugzilla's mailgateway
   *
   * <code>
   *   uses('org.bugzilla.gateway.BugMessage', 'peer.mail.transport.MailTransport');
   *
   *   $m= &new BugMessage(); {
   *     $m->setFrom(new InternetAddress('friebe@example.com', 'Timm Friebe'));
   *     $m->addRecipient(TO, new InternetAddress('bugzilla@example.com'));
   *     $m->setSubject('Bug');
   *     $m->setProduct('MyProduct');
   *     $m->setComponent('My Component');
   *     $m->setShort_desc('Login page');
   *     $m->setBug_severity(BUG_SEVERITY_ENHANCEMENT);
   *     $m->setBody('This seems to be entirely broken. It shows a MySQL error.');
   *   }
   *
   *   $t= &new MailTransport();
   *   try(); {
   *     $t->connect();
   *     $t->send($m);
   *     $t->close();
   *   } if (catch('TransportException', $e)) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   *
   *   exit(0);
   * </code>
   *
   * @see      xp://peer.mail.Message
   * @purpose  Specialized mail message
   */
  class BugMessage extends Message {
    var
      $tokens= array();


    /**
     * Set Product
     *
     * @access  public
     * @param   string product
     */
    function setProduct($product) {
      $this->tokens['product']= $product;
    }

    /**
     * Set Component
     *
     * @access  public
     * @param   string component
     */
    function setComponent($component) {
      $this->tokens['component']= $component;
    }

    /**
     * Set Short_desc
     *
     * @access  public
     * @param   string short_desc
     */
    function setShort_desc($short_desc) {
      $this->tokens['short_desc']= $short_desc;
    }

    /**
     * Set Rep_platform
     *
     * @access  public
     * @param   string rep_platform
     */
    function setRep_platform($rep_platform) {
      $this->tokens['rep_platform']= $rep_platform;
    }

    /**
     * Set Bug_severity
     *
     * @access  public
     * @param   string bug_severity
     */
    function setBug_severity($bug_severity) {
      $this->tokens['bug_severity']= $bug_severity;
    }

    /**
     * Set Priority
     *
     * @access  public
     * @param   string priority
     */
    function setPriority($priority) {
      $this->tokens['priority']= $priority;
    }

    /**
     * Set Op_sys
     *
     * @access  public
     * @param   string op_sys
     */
    function setOp_sys($op_sys) {
      $this->tokens['op_sys']= $op_sys;
    }

    /**
     * Set Version
     *
     * @access  public
     * @param   string version
     */
    function setVersion($version) {
      $this->tokens['version']= $version;
    }
    
    /**
     * Suppress report mail when successful
     *
     * @access  public
     * @param   bool suppress
     */
    function setNoReport($b= TRUE) {
      $this->tokens['nomail']= ($b ? 'yes' : 'no');
    }    

    /**
     * Get message body. If this message is contained in a folder and the body
     * has'nt been fetched yet, it'll be retrieved from the storage underlying
     * the folder.
     *
     * @access  public
     * @return  string
     */
    function getBody() {
      $header= '';
      foreach ($this->tokens as $key => $val) {
        $header.= '@'.$key.':'.$val."\n";
      }

      return $header."\n".parent::getBody();
    }    
  }
?>
