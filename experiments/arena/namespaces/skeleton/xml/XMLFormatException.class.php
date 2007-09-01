<?php
/* This class is part of the XP framework
 *
 * $Id: XMLFormatException.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace xml;

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
  class XMLFormatException extends lang::FormatException {
    public
      $type        = 0,
      $filename    = '',
      $linenumber  = 0,
      $column      = 0;
  
    /**
     * Constructor
     *
     * @param   string message
     * @param   int type default XML_ERROR_SYNTAX
     * @param   string filename default NULL
     * @param   int linenumber
     * @param   int column
     */
    public function __construct(
      $message, 
      $type = ,
      $filename = ,
      $linenumber = 0,
      $column = 0
    ) {
      parent::__construct($message);
      $this->type= $type;
      $this->filename= $filename;
      $this->linenumber= $linenumber;
      $this->column= $column;
    }
    
    /**
     * Return compound message of this exception.
     *
     * @return  string
     */
    public function compoundMessage() {
      return sprintf(
        "%s@('%s'){\n".
        "  type       %d (%s)\n".
        "  filename   %s\n".
        "  linenumber %d\n".
        "  column     %d\n".
        "}\n",
        $this->getClassName(),
        $this->getMessage(),
        $this->getType(),
        $this->getTypeName(),
        ::xp::stringOf($this->getfilename()),
        $this->getlinenumber(),
        $this->getColumn()
      );
    }

    /**
     * Get Type
     *
     * @return  int
     */
    public function getType() {
      return $this->type;
    }
    
    /**
     * Get Type's name
     *
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
     * Get filename
     *
     * @return  string
     */
    public function getFilename() {
      return $this->filename;
    }

    /**
     * Get line number
     *
     * @return  int
     */
    public function getLineNumber() {
      return $this->linenumber;
    }

    /**
     * Get Column
     *
     * @return  int
     */
    public function getColumn() {
      return $this->column;
    }  
  }
?>
