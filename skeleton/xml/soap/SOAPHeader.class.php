<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * SOAP Header interface
   *
   * @see      xp://xml.soap.SOAPHeaderElement
   * @purpose  Interface
   */
  class SOAPHeader extends Interface {

    /**
     * Retrieve XML representation of this header for use in a SOAP
     * message.
     *
     * @access  public
     * @param   array<string, string> ns list of namespaces
     * @return  &xml.Node
     */
    function &getNode($ns) { }
  }
?>
