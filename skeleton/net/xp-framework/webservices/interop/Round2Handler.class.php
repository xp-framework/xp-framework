<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Handles SOAP Interop requests as proposed by
   * Whitemesa.
   *
   * @see      http://www.whitemesa.com/interop/proposal2.html
   * @purpose  SOAP Interop server
   */
  class Round2Handler extends Object {
  
    /**
     * Echoes a given string.
     *
     * @access  public
     * @param   string 
     * @return  string
     */
    function echoString($inputString) {
      return (string)$inputString;
    }
    
    /**
     * Echoes a given string array.
     *
     * @access  public
     * @param   string[] 
     * @return  string[]
     * @throws  lang.IllegalArgumentException
     */
    function echoStringArray($inputStringArray) {
      if (!$this->_isTypeOf('string',is_array($inputStringArray)) {
        return throw (new IllegalArgumentException('Parameter must be an array ('.xp::typeOf($inputStringArray).' given)');
      }
      
      return $inputStringArray;
    }
    
    /**
     * Echoes an integer
     *
     * @access  public
     * @param   int
     * @return  int
     */
    function echoInteger($inputInteger) {
      return (int)$inputInteger;
    }
    
    /**
     * Echoes an array of integers
     *
     * @access  public
     * @param   int[]
     * @return  int[]
     * @throws  lang.IllegalArgumentException
     */
    function echoIntegerArray($inputIntegerArray) {
      if (!is_array($inputStringArray)) {
        return throw (new IllegalArgumentException('Parameter must be an array ('.xp::typeOf($inputStringArray).' given)');
      }
      
      return $inputIntegerArray;
    }
    
    /**
     * Echoes a float
     *
     * @access  public
     * @param   float
     * @return  float
     */
    function echoFloat($inputFloat) {
      return (float)$inputFloat;
    }
    
    /**
     * Echoes an array of floats
     *
     * @access  public
     * @param   float[]
     * @return  float[]
     * @throws  lang.IllegalArgumentException
     */
    function echoFloatArray($inputFloatArray) {
      if (!is_array($inputStringArray)) {
        return throw (new IllegalArgumentException('Parameter must be an array ('.xp::typeOf($inputStringArray).' given)');
      }
      
      return $inputFloatArray;
    }
    
    /**
     * Echoes a struct.
     *
     * @access  public
     * @param   mixed[] structure
     * @return  mixed[]
     * @throws  lang.IllegalArgumentException
     */
    function echoStruct($inputStruct) {
      if (
        !is_array($inputStruct) ||
        !isset($inputStruct['varString']) ||
        !isset($inputStruct['varInt']) ||
        !isset($inputStruct['varFloat'])
      ) {
        return throw (new IllegalArgumentException('Parameter is not the struct from specification!'));
      }
      
      return $inputStruct;
    }
    
    /**
     * Echoes an array of structs
     *
     * @access  public
     * @param   mixed[]
     * @return  mixed[]
     * @throws  lang.MethodNotImplementedException
     */
    function echoStructArray($inputStructArray) {
      return throw (new MethodNotImplementedException('Not implemented'));
    }
    
    /**
     * Echoes a void.
     *
     * @access  public
     * @return  NULL
     */
    function echoVoid() {
      return NULL;
    }
    
    /**
     * Echoes a base64 string
     *
     * @access  public
     * @param   string
     * @return  string
     * @throws  lang.MethodNotImplementedException
     */
    function echoBase64($inputBase64) {
      return throw (new MethodNotImplementedException('Not implemented'));
    }
    
    /**
     * Echoes a hexbinary.
     *
     * @access  public
     * @param   string 
     * @return  string
     * @throws  lang.MethodNotImplementedException
     */
    function echoHexBinary($inputHexBinary) {
      return throw (new MethodNotImplementedException('Not implemented'));
    }
    
    /**
     * Echoes a date.
     *
     * @access  public
     * @param   &util.Date
     * @return  &util.Date
     * @throws  lang.IllegalArgumentException
     */
    function echoDate($inputDate) {
      if (!is('util.Date', $inputDate)) {
        return throw (new IllegalArgumentException('Given parameter is not a date!'));
      }
      
      return $inputDate;
    }
    
    /**
     * Echoes a decimal.
     *
     * @access  public
     * @param   float
     * @return  float
     */
    function echoDecimal($inputDecimal) {
      return $inputDecimal;
    }
    
    /**
     * Echoes a boolean
     *
     * @access  public
     * @param   boolean
     * @return  boolean
     */
    function echoBoolean($inputBoolean) {
      return (bool)$inputBoolean;
    }
  }
?>
