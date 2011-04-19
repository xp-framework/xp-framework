<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('text.parser.DaemonMailParserException');

  /**
   * Generic DaemonMailParser exception
   *
   * @purpose  Exception in DaemonMailParser
   */
  class DaemonMailParserAutoresponderException extends DaemonMailParserException {
    public
      $sourceMessage  = NULL;
      
    /**
     * Constructor.
     *
     * @param   string errormessage
     * @param   peer.mail.Message sourceMessage default NULL
     */
    public function __construct($errormessage, $sourceMessage= NULL) {
      parent::__construct($errormessage);
      $causingMessage && $this->sourceMessage= $sourceMessage;
    }
  }
?>
