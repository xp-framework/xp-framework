<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Indicates the XML cannot be parsed (i.e., it is not well-formed). 
   *
   * The error type can be retrieved via the getType() method and will
   * be one of the following constants:
   * <pre>
   *   XML_ERROR_NONE                         
   *   XML_ERROR_NO_MEMORY                    
   *   XML_ERROR_SYNTAX                       
   *   XML_ERROR_NO_ELEMENTS                  
   *   XML_ERROR_INVALID_TOKEN                
   *   XML_ERROR_UNCLOSED_TOKEN               
   *   XML_ERROR_PARTIAL_CHAR                 
   *   XML_ERROR_TAG_MISMATCH                 
   *   XML_ERROR_DUPLICATE_ATTRIBUTE          
   *   XML_ERROR_JUNK_AFTER_DOC_ELEMENT       
   *   XML_ERROR_PARAM_ENTITY_REF             
   *   XML_ERROR_UNDEFINED_ENTITY             
   *   XML_ERROR_RECURSIVE_ENTITY_REF         
   *   XML_ERROR_ASYNC_ENTITY                 
   *   XML_ERROR_BAD_CHAR_REF                 
   *   XML_ERROR_BINARY_ENTITY_REF            
   *   XML_ERROR_ATTRIBUTE_EXTERNAL_ENTITY_REF
   *   XML_ERROR_MISPLACED_XML_PI             
   *   XML_ERROR_UNKNOWN_ENCODING             
   *   XML_ERROR_INCORRECT_ENCODING           
   *   XML_ERROR_UNCLOSED_CDATA_SECTION       
   *   XML_ERROR_EXTERNAL_ENTITY_HANDLING        
   * </pre>
   *
   * @purpose  Exception
   */
  class XMLFormatException extends FormatException {
    public
      $type     = 0,
      $file     = '',
      $line     = 0,
      $column   = 0;
  
    /**
     * Constructor
     *
     * @access  public
     * @param   string message
     * @param   int type default XML_ERROR_SYNTAX
     * @param   string file default NULL
     * @param   int line
     * @param   int column
     */
    public function __construct(
      $message, 
      $type = XML_ERROR_SYNTAX,
      $file = NULL,
      $line = 0,
      $column = 0
    ) {
      parent::__construct($message);
      $this->type= $type;
      $this->file= $file;
      $this->line= $line;
      $this->column= $column;
    }
    
    /**
     * Returns a string representation of this exception
     *
     * @access  public
     * @return  string
     */
    public function toString() {      
      $s= sprintf(
        "%s@('%s'){\n".
        "  type     %d (%s)\n".
        "  file     %s\n".
        "  line     %d\n".
        "  column   %d\n".
        "}\n",
        self::getClassName(),
        self::getMessage(),
        self::getType(),
        self::getTypeName(),
        var_export(self::getFile(), 1),
        self::getLine(),
        self::getColumn()
      );
      for ($i= 0, $t= sizeof($this->trace); $i < $t; $i++) {
        $s.= $this->trace[$i]->toString();
      }
      return $s;
    }

    /**
     * Get Type
     *
     * @access  public
     * @return  int
     */
    public function getType() {
      return $this->type;
    }
    
    /**
     * Get Type's name
     *
     * @access  public
     * @return  string
     */
    public function getTypeName() {
      static $types= array(
        XML_ERROR_NONE                           => 'NONE',
        XML_ERROR_NO_MEMORY                      => 'NO_MEMORY',
        XML_ERROR_SYNTAX                         => 'SYNTAX',
        XML_ERROR_NO_ELEMENTS                    => 'NO_ELEMENTS',
        XML_ERROR_INVALID_TOKEN                  => 'INVALID_TOKEN',
        XML_ERROR_UNCLOSED_TOKEN                 => 'UNCLOSED_TOKEN',
        XML_ERROR_PARTIAL_CHAR                   => 'PARTIAL_CHAR',
        XML_ERROR_TAG_MISMATCH                   => 'TAG_MISMATCH',
        XML_ERROR_DUPLICATE_ATTRIBUTE            => 'DUPLICATE_ATTRIBUTE',
        XML_ERROR_JUNK_AFTER_DOC_ELEMENT         => 'JUNK_AFTER_DOC_ELEMENT',
        XML_ERROR_PARAM_ENTITY_REF               => 'PARAM_ENTITY_REF',
        XML_ERROR_UNDEFINED_ENTITY               => 'UNDEFINED_ENTITY',
        XML_ERROR_RECURSIVE_ENTITY_REF           => 'RECURSIVE_ENTITY_REF',
        XML_ERROR_ASYNC_ENTITY                   => 'ASYNC_ENTITY',
        XML_ERROR_BAD_CHAR_REF                   => 'BAD_CHAR_REF',
        XML_ERROR_BINARY_ENTITY_REF              => 'BINARY_ENTITY_REF',
        XML_ERROR_ATTRIBUTE_EXTERNAL_ENTITY_REF  => 'ATTRIBUTE_EXTERNAL_ENTITY_REF',
        XML_ERROR_MISPLACED_XML_PI               => 'MISPLACED_XML_PI',
        XML_ERROR_UNKNOWN_ENCODING               => 'UNKNOWN_ENCODING',
        XML_ERROR_INCORRECT_ENCODING             => 'INCORRECT_ENCODING',
        XML_ERROR_UNCLOSED_CDATA_SECTION         => 'UNCLOSED_CDATA_SECTION',
        XML_ERROR_EXTERNAL_ENTITY_HANDLING       => 'EXTERNAL_ENTITY_HANDLING',
      );
      return $types[$this->type];
    }

    /**
     * Get File
     *
     * @access  public
     * @return  string
     */
    public function getFile() {
      return $this->file;
    }

    /**
     * Get Line
     *
     * @access  public
     * @return  int
     */
    public function getLine() {
      return $this->line;
    }

    /**
     * Get Column
     *
     * @access  public
     * @return  int
     */
    public function getColumn() {
      return $this->column;
    }  
  }
?>
