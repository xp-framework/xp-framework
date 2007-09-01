<?php
/* This class is part of the XP framework
 *
 * $Id: DaemonMailParserException.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace text::parser;

  /**
   * Generic DaemonMailParser exception
   *
   * @purpose  Exception in DaemonMailParser
   */
  class DaemonMailParserException extends lang::XPException {
    public
      $sourceMessage  = NULL;
      
    /**
     * Constructor.
     *
     * @param   string errormessage
     * @param   peer.mail.Message sourceMessage default NULL
     */
    public function __construct($errormessage, $sourceMessage= ) {
      parent::__construct($errormessage);
      $causingMessage && $this->sourceMessage= $sourceMessage;
    }
  }
?>
