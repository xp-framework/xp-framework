<?php
  uses('xml.XML');
  
  class XMLParser extends XML {
    var
      $parser,
      $error,
      $dataSource;
      
    var
      $callback;
      
    function XMLParser($params= NULL) {
      $this->__construct($params);
    }
    
    function __construct($params= NULL) {
      parent::__construct();
      $this->parser= $this->error= $this->dataSource= NULL;
    }
    
    function &_create() {
      $this->parser = xml_parser_create();
      xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, FALSE);
      return $this->parser;
    }
    
    function _free() {
      if (is_resource($this->parser)) return xml_parser_free($this->parser);
    }
    
    function &_error() {
      $this->error= new StdClass();
      $this->error->type= xml_get_error_code($this->parser);
      $this->error->message= xml_error_string($this->error->type);
      $this->error->file= $this->dataSource;
      $this->error->line= xml_get_current_line_number($this->parser);
      $this->error->column= xml_get_current_column_number($this->parser);
      return $this->error;
    }
    
    function parse($data) {
      unset($this->error);
      if (NULL == $this->parser) $this->_create();
      if (!isset($this->callback) || !is_object($this->callback)) return throw(new IllegalArgumentException(
        $this->callback.' is not an object'
      ));
      
      xml_set_object($this->parser, $this->callback);
      xml_set_element_handler($this->parser, '_pCallStartElement', '_pCallEndElement');
      xml_set_character_data_handler($this->parser, '_pCallCData');
      xml_set_default_handler($this->parser, '_pCallDefault');

      if (!xml_parse($this->parser, $data)) return throw(new FormatException($this->_error()));
      return 1;
    }
    
    function __destruct() {
      $this->_free();
      parent::__destruct();
    }
  }
?>
