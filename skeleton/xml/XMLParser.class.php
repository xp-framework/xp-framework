<?php
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
      xml_set_object($this->parser, $this);
      xml_set_element_handler($this->parser, '_startElement', '_endElement');
      xml_set_character_data_handler($this->parser, '_CData');
      xml_set_default_handler($this->parser, '_defaultHandler');
      xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, FALSE);
      return $this->parser;
    }
    
    function _free() {
      if (NULL != $this->parser) return xml_parser_free($this->parser);
    }
    
    function _callMethod($methodName, $attributes) {      
      $methodName= '_pCall'.$methodName;
      if (method_exists($this->callback, $methodName)) {
        $this->callback->$methodName($attributes);
      }
    }
    
    function _startElement($parser, $name, $attrs) {
      $this->_callMethod('StartElement', array(
        'name'  => $name,
        'attrs' => $attrs
      ));
    }
   
    function _endElement($parser, $name) {
      $this->_callMethod('EndElement', array(
        'name'  => $name
      ));
    }

    function _CData($parser, $cdata) {
      $this->_callMethod('CData', array(
        'cdata' => $cdata
      ));
    }

    function _defaultHandler($parser, $data) {
      $this->_callMethod('Default', array(
        'data'  => $data
      ));
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
    
    function parse($data, $isFinal= 0) {
      unset($this->error);
      if (NULL == $this->parser) $this->_create();
      if (!isset($this->callback) || !is_object($this->callback)) return throw(
        E_PARAM_EXCEPTION,
        $this->callback.' is not an object'
      );
      if (!xml_parse($this->parser, $data, $isFinal)) return throw(
        E_FORMAT_EXCEPTION,
        $this->_error()
      );
      return 1;
    }
    
    function __destruct() {
      $this->_free();
      parent::__destruct();
    }
  }
?>
