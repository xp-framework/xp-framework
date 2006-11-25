<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Base interface for all UDDI commands
   *
   * @purpose  Interface
   * @see      xp://xml.uddi.InquiryCommand
   * @see      xp://xml.uddi.PublishCommand
   */
  interface UDDICommand {

    /**
     * Marshal command to a specified node
     *
     * @access  public
     * @param   &xml.Node node
     */
    public function marshalTo(&$node);

    /**
     * Unmarshal return value from a specified node
     *
     * @access  public
     * @param   &xml.Node node
     * @return  &lang.Object
     */
    public function &unmarshalFrom(&$node);
  }
?>
