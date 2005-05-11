<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xml.XML', 'xml.XMLFormatException');
  
  /**
   * XML Parser
   *
   * Example:
   * <code>
   *   uses('xml.parser.XMLParser');
   *
   *   $parser= &new XMLParser();
   *   try(); {
   *     $parser->parse($xml);
   *   } if (catch('XMLFormatException', $e)) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   * </code>
   *
   * @purpose  Parse XML
   */
  class XMLParser extends Object {
    var
      $encoding     = '',
      $callback     = NULL;
    

    /**
     * Constructor
     *
     * @access  public
     * @param   string encoding default ''
     */
    function __construct($encoding= '') {
      $this->encoding= $encoding;
    }

    /**
     * Set callback
     *
     * @access  public
     * @param   &xml.parser.ParserCallback callback
     */
    function setCallback(&$callback) {
      $this->callback= &$callback;
    }

    /**
     * Set Encoding
     *
     * @access  public
     * @param   string encoding
     */
    function setEncoding($encoding) {
      $this->encoding= $encoding;
    }

    /**
     * Get Encoding
     *
     * @access  public
     * @return  string
     */
    function getEncoding() {
      return $this->encoding;
    }
    
    /**
     * Parse XML data
     *
     * @access  public
     * @param   string data
     * @param   string source default NULL optional source identifier, will show up in exception
     * @return  bool
     * @throws  xml.XMLFormatException in case the data could not be parsed
     * @throws  lang.NullPointerException in case a parser could not be created
     */
    function parse($data, $source= NULL) {
      if ($parser = xml_parser_create($this->encoding)) {
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, FALSE);

        // Register callback
        if ($this->callback) {
          xml_set_object($parser, $this->callback);
          xml_set_element_handler($parser, 'onStartElement', 'onEndElement');
          xml_set_character_data_handler($parser, 'onCData');
          xml_set_default_handler($parser, 'onDefault');
        }
      
        // Parse data
        if (!xml_parse($parser, $data, TRUE)) {
          $type= xml_get_error_code($parser);
          $line= xml_get_current_line_number($parser);
          $column= xml_get_current_column_number($parser);
          xml_parser_free($parser);
          return throw(new XMLFormatException(
            xml_error_string($type),
            $type,
            $source,
            $line,
            $column
          ));
        }
        xml_parser_free($parser);
        return TRUE;
      }

      return throw(new NullPointerException('Could not create parser'));
    }
  }
?>
