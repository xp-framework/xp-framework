<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Classes implementing this interface may be used as callbacks
   * for XMLParser. 
   *
   * @see      xp://xml.parser.XMLParser#setCallBack
   * @purpose  Interface
   */
  class ParserCallback extends Interface {
  
    /**
     * Callback function for XMLParser
     *
     * @access  public
     * @param   resource parser
     * @param   string name
     * @param   array attrs
     */
    function onStartElement($parser, $name, $attrs) { }
    
    /**
     * Callback function for XMLParser
     *
     * @access  public
     * @param   resource parser
     * @param   string name
     */
    function onEndElement($parser, $name) { }

    /**
     * Callback function for XMLParser
     *
     * @access  public
     * @param   resource parser
     * @param   string cdata
     */
    function onCData($parser, $cdata) { }

    /**
     * Callback function for XMLParser
     *
     * @access  public
     * @param   resource parser
     * @param   string data
     */
    function onDefault($parser, $data) { }
  }
?>
