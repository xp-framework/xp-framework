<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Generic DaemonMailParser exception
   *
   * @purpose  Exception in DaemonMailParser
   */
  class DaemonMailParserAutoresponderException extends DaemonMailParserException {
    var
      $sourceMessage  = NULL;
      
    /**
     * Constructor.
     *
     * @access  public
     * @param   string errormessage
     * @param   peer.mail.Message sourceMessage default NULL
     */
    function __construct($errormessage, $sourceMessage= NULL) {
      parent::__construct($errormessage);
      $causingMessage && $this->sourceMessage= $sourceMessage;
    }
  }
?>
