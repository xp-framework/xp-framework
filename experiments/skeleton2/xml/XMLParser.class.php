<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'xml.XML',
    'xml.XMLFormatException'
  );

  /**
   * XML Parser
   *
   * @purpose  Parse XML
   */
  class XMLParser extends XML {
    public
      $parser       = NULL,
      $dataSource   = NULL,
      $callback     = NULL;

    /**
     * Constructor
     *
     * @access  public
     * @param   array params default NULL
     */      
    public function __construct($params= NULL) {
      parent::__construct();
      $this->parser= $this->dataSource= NULL;
    }
    
    /**
     * Create this parser
     *
     * @access  private
     * @return  &resource parser handles
     */
    private function _create() {
      $this->parser = xml_parser_create();
      xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, FALSE);
      return $this->parser;
    }
    
    /**
     * Set callback
     *
     * @access  public
     * @param   &lang.Object callback
     */
    public function setCallback(Object $callback) {
      $this->callback= $callback;
    }
    
    /**
     * Free this parser
     *
     * @access  private
     */
    private function _free() {
      if (is_resource($this->parser)) return xml_parser_free($this->parser);
    }
    
    /**
     * Parse
     *
     * @access  public
     * @param   string data
     * @return  bool
     * @throws  lang.IllegalArgumentException in case there is no valid callback
     * @throws  xml.XMLFormatException in case the data could not be parsed
     */
    public function parse($data) {
      if (NULL == $this->parser) self::_create();
      if (!isset($this->callback) || !is_object($this->callback)) {
        throw (new IllegalArgumentException('Callback is not an object'));
      }
      
      xml_set_object($this->parser, $this->callback);
      xml_set_element_handler($this->parser, 'onStartElement', 'onEndElement');
      xml_set_character_data_handler($this->parser, 'onCData');
      xml_set_default_handler($this->parser, 'onDefault');

      if (!xml_parse($this->parser, $data)) {
        $type= xml_get_error_code($this->parser);
        throw (new XMLFormatException(
          xml_error_string($type),
          $type,
          $this->dataSource,
          xml_get_current_line_number($this->parser),
          xml_get_current_column_number($this->parser)
        ));
      }
         
      return TRUE;
    }
    
    /**
     * Destructor
     *
     * @access  public
     */
    public function __destruct() {
      self::_free();
      parent::__destruct();
    }
  }
?>
