<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.MethodNotImplementedException');

  /**
   * Handles SOAP Interop requests as proposed by
   * Whitemesa.
   *
   * @see      http://www.whitemesa.com/interop/proposal2.html
   * @purpose  SOAP Interop server
   */
  class Round2Handler extends Object {

    /**
     * Checks the type of given object.
     *
     * @access  private
     * @param   string type
     * @param   &mixed
     * @throws  lang.IllegalArgumentException
     */
    function _assertType($type, &$object) {
      if ($type != xp::typeOf($object))
        return throw (new IllegalArgumentException('Object not of expected type '.$type.', but '.xp::typeOf($object).' with value '.$object));
    }
    
    /**
     * Checks all entries in an array for correct type
     *
     * @access  private
     * @param   string type
     * @param   &array
     * @
     */
    function _assertSubtype($type, &$array) {
      foreach (array_keys($array) as $key) {
        if ($type != xp::typeOf($array[$key]))
          return throw (new IllegalArgumentException('Object (in array) not of expected type '.$type.', but '.xp::typeOf($array[$key]).' with value '.$array[$key]));
      }
    }
  
    /**
     * Echoes a given string.
     *
     * @access  public
     * @param   string 
     * @return  string
     */
    function echoString($inputString) {
      $this->_assertType('string', $inputString);
      return $inputString;
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
      $this->_assertType('array', $inputStringArray);
      $this->_assertSubtype('string', $inputStringArray);
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
      $this->_assertType('integer', $inputInteger);
      return $inputInteger;
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
      $this->_assertType('array', $inputIntegerArray);
      $this->_assertSubtype('integer', $inputIntegerArray);
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
      $this->_assertType('double', $inputFloat);
      return $inputFloat;
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
      $this->_assertType('array', $inputFloatArray);
      $this->_assertSubtype('double', $inputFloatArray);
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
      $this->_assertType('array',   $inputStruct);
      $this->_assertType('string',  $inputStruct['varString']);
      $this->_assertType('integer', $inputStruct['varInt']);
      $this->_assertType('double',   $inputStruct['varFloat']);
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
      $this->_assertType('util.Date', $inputType);
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
      $this->_assertType('double', $inputDecimal);
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
      $this->_assertType('boolean', $inputBoolean);
      return (bool)$inputBoolean;
    }
  }
?>
