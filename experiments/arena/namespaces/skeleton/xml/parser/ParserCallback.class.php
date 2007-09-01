<?php
/* This class is part of the XP framework
 *
 * $Id: ParserCallback.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace xml::parser;

  /**
   * Classes implementing this interface may be used as callbacks
   * for XMLParser. 
   *
   * @see      xp://xml.parser.XMLParser#setCallBack
   * @purpose  Interface
   */
  interface ParserCallback {
  
    /**
     * Callback function for XMLParser
     *
     * @param   resource parser
     * @param   string name
     * @param   array attrs
     */
    public function onStartElement($parser, $name, $attrs);
    
    /**
     * Callback function for XMLParser
     *
     * @param   resource parser
     * @param   string name
     */
    public function onEndElement($parser, $name);

    /**
     * Callback function for XMLParser
     *
     * @param   resource parser
     * @param   string cdata
     */
    public function onCData($parser, $cdata);

    /**
     * Callback function for XMLParser
     *
     * @param   resource parser
     * @param   string data
     */
    public function onDefault($parser, $data);
  }
?>
