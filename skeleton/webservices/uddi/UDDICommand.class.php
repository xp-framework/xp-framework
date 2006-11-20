<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Base interface for all UDDI commands
   *
   * @purpose  Interface
   * @see      xp://webservices.uddi.InquiryCommand
   * @see      xp://webservices.uddi.PublishCommand
   */
  class UDDICommand extends Interface {

    /**
     * Marshal command to a specified node
     *
     * @access  public
     * @param   &xml.Node node
     */
    function marshalTo(&$node) { }

    /**
     * Unmarshal return value from a specified node
     *
     * @access  public
     * @param   &xml.Node node
     * @return  &lang.Object
     */
    function &unmarshalFrom(&$node) { }
  }
?>
