<?php
/* This class is part of the XP framework
 *
 * $Id: UDDICommand.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace webservices::uddi;

  /**
   * Base interface for all UDDI commands
   *
   * @purpose  Interface
   * @see      xp://webservices.uddi.InquiryCommand
   * @see      xp://webservices.uddi.PublishCommand
   */
  interface UDDICommand {

    /**
     * Marshal command to a specified node
     *
     * @param   xml.Node node
     */
    public function marshalTo($node);

    /**
     * Unmarshal return value from a specified node
     *
     * @param   xml.Node node
     * @return  lang.Object
     */
    public function unmarshalFrom($node);
  }
?>
