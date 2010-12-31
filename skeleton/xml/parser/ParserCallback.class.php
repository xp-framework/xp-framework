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

    /**
     * Callback function for XMLParser
     *
     * @param   xml.parser.XMLParser instance
     */
    public function onBegin($instance);

    /**
     * Callback function for XMLParser
     *
     * @param   xml.parser.XMLParser instance
     * @param   xml.XMLFormatException exception
     */
    public function onError($instance, $exception);

    /**
     * Callback function for XMLParser
     *
     * @param   xml.parser.XMLParser instance
     */
    public function onFinish($instance);
  }
?>
