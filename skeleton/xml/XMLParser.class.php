<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xml.XML');
  
  /**
   * XML Parser
   *
   * @purpose  Parse XML
   */
  class XMLParser extends XML {
    var
      $parser       = NULL,
      $error        = NULL,
      $dataSource   = NULL,
      $callback     = NULL;

    /**
     * Constructor
     *
     * @access  public
     * @param   array params default NULL
     */      
    function __construct($params= NULL) {
      parent::__construct();
      $this->parser= $this->error= $this->dataSource= NULL;
    }
    
    /**
     * Create this parser
     *
     * @access  private
     * @return  &resource parser handles
     */
    function &_create() {
      $this->parser = xml_parser_create();
      xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, FALSE);
      return $this->parser;
    }
    
    /**
     * Free this parser
     *
     * @access  private
     */
    function _free() {
      if (is_resource($this->parser)) return xml_parser_free($this->parser);
    }
    
    /**
     * Returns error message
     *
     * @access  privtae
     * @return  string errormessage
     */
    function &_error() {
      $this->error= &new StdClass();
      $this->error->type= xml_get_error_code($this->parser);
      $this->error->message= xml_error_string($this->error->type);
      $this->error->file= $this->dataSource;
      $this->error->line= xml_get_current_line_number($this->parser);
      $this->error->column= xml_get_current_column_number($this->parser);

      return sprintf(
        "XML parser error #%d on line %d offset %d: %s",
        $this->error->type,
        $this->error->line,
        $this->error->column,
        $this->error->message
      );    
    }
    
    /**
     * Parse
     *
     * @access  public
     * @param   string data
     * @return  bool
     * @throws  lang.IllegalArgumentException in case there is no valid callback
     * @throws  lang.FormatException in case the data could not be parsed
     */
    function parse($data) {
      unset($this->error);
      if (NULL == $this->parser) $this->_create();
      if (!isset($this->callback) || !is_object($this->callback)) return throw(new IllegalArgumentException(
        'callback is not an object'
      ));
      
      xml_set_object($this->parser, $this->callback);
      xml_set_element_handler($this->parser, '_pCallStartElement', '_pCallEndElement');
      xml_set_character_data_handler($this->parser, '_pCallCData');
      xml_set_default_handler($this->parser, '_pCallDefault');

      if (!xml_parse($this->parser, $data)) {
        return throw(new FormatException($this->_error()));
      }
         
      return TRUE;
    }
    
    /**
     * Destructor
     *
     * @access  public
     */
    function __destruct() {
      $this->_free();
      parent::__destruct();
    }
  }
?>
