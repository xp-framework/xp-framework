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
     * @param   string type
     * @param   &mixed object
     * @throws  lang.IllegalArgumentException
     */
    protected function _assertType($type, $object) {
      if ($type != xp::typeOf($object))
        throw (new IllegalArgumentException('Object not of expected type '.$type.', but '.xp::typeOf($object).' with value '.var_export($object, 1)));
    }
    
    /**
     * Checks all entries in an array for correct type
     *
     * @param   string type
     * @param   &array array
     * @throws  lang.IllegalArgumentException
     */
    protected function _assertSubtype($type, $array) {
      foreach (array_keys($array) as $key) {
        if ($type != xp::typeOf($array[$key]))
          throw (new IllegalArgumentException('Object (in array) not of expected type '.$type.', but '.xp::typeOf($array[$key]).' with value '.var_export($array[$key], 1)));
      }
    }
  
    /**
     * Echoes a given string.
     *
     * @param   string inputString
     * @return  string
     */
    #[@webmethod]
    public function echoString($inputString) {
      $this->_assertType('string', $inputString);
      return $inputString;
    }
    
    /**
     * Echoes a given string array.
     *
     * @param   string[] inputStringArray
     * @return  string[]
     * @throws  lang.IllegalArgumentException
     */
    #[@webmethod]
    public function echoStringArray($inputStringArray) {
      $this->_assertType('array', $inputStringArray);
      $this->_assertSubtype('string', $inputStringArray);
      return $inputStringArray;
    }
    
    /**
     * Echoes an integer
     *
     * @param   int inputInteger
     * @return  int
     */
    #[@webmethod]
    public function echoInteger($inputInteger) {
      $this->_assertType('integer', $inputInteger);
      return $inputInteger;
    }
    
    /**
     * Echoes an array of integers
     *
     * @param   int[] inputIntegerArray
     * @return  int[]
     * @throws  lang.IllegalArgumentException
     */
    #[@webmethod]
    public function echoIntegerArray($inputIntegerArray) {
      $this->_assertType('array', $inputIntegerArray);
      $this->_assertSubtype('integer', $inputIntegerArray);
      return $inputIntegerArray;
    }
    
    /**
     * Echoes a float
     *
     * @param   float inputFloat
     * @return  float
     */
    #[@webmethod]
    public function echoFloat($inputFloat) {
      $this->_assertType('double', $inputFloat);
      return $inputFloat;
    }
    
    /**
     * Echoes an array of floats
     *
     * @param   float[] inputFloatArray
     * @return  float[]
     * @throws  lang.IllegalArgumentException
     */
    #[@webmethod]
    public function echoFloatArray($inputFloatArray) {
      $this->_assertType('array', $inputFloatArray);
      $this->_assertSubtype('double', $inputFloatArray);
      return $inputFloatArray;
    }
    
    /**
     * Echoes a struct.
     *
     * @param   mixed[] inputStruct
     * @return  mixed[]
     * @throws  lang.IllegalArgumentException
     */
    #[@webmethod]
    public function echoStruct($inputStruct) {
      $this->_assertType('array',   $inputStruct);
      $this->_assertType('string',  $inputStruct['varString']);
      $this->_assertType('integer', $inputStruct['varInt']);
      $this->_assertType('double',  $inputStruct['varFloat']);
      return $inputStruct;
    }
    
    /**
     * Echoes an array of structs
     *
     * @param   mixed[] inputStructArray
     * @return  mixed[]
     * @throws  lang.MethodNotImplementedException
     */
    #[@webmethod]
    public function echoStructArray($inputStructArray) {
      $this->_assertType('array', $inputStructArray);
      foreach ($inputStructArray as $singleStruct) {
        $this->_assertType('string',  $singleStruct['varString']);
        $this->_assertType('integer', $singleStruct['varInt']);
        $this->_assertType('double',  $singleStruct['varFloat']);
      }
      return $inputStructArray;
    }
    
    /**
     * Echoes a void.
     *
     * @return  NULL
     */
    #[@webmethod]
    public function echoVoid() {
      return NULL;
    }
    
    /**
     * Echoes a base64 string
     *
     * @param   string inputBase64
     * @return  string
     * @throws  lang.MethodNotImplementedException
     */
    #[@webmethod]
    public function echoBase64($inputBase64) {
      $this->_assertType('webservices.soap.types.SOAPBase64Binary', $inputBase64);
      return $inputBase64;
    }
    
    /**
     * Echoes a hexbinary.
     *
     * @param   string  inputHexBinary
     * @return  string
     * @throws  lang.MethodNotImplementedException
     */
    #[@webmethod]
    public function echoHexBinary($inputHexBinary) {
      $this->_assertType('webservices.soap.types.SOAPHexBinary', $inputHexBinary);
      return $inputHexBinary;
    }
    
    /**
     * Echoes a date.
     *
     * @param   &util.Date inputDate
     * @return  &util.Date
     * @throws  lang.IllegalArgumentException
     */
    #[@webmethod]
    public function echoDate($inputDate) {
      $this->_assertType('util.Date', $inputDate);
      return $inputDate;
    }
    
    /**
     * Echoes a decimal.
     *
     * @param   float inputDecimal
     * @return  float
     */
    #[@webmethod]
    public function echoDecimal($inputDecimal) {
      $this->_assertType('double', $inputDecimal);
      return $inputDecimal;
    }
    
    /**
     * Echoes a boolean
     *
     * @param   bool inputBoolean
     * @return  bool
     */
    #[@webmethod]
    public function echoBoolean($inputBoolean) {
      $this->_assertType('boolean', $inputBoolean);
      return (bool)$inputBoolean;
    }
  }
?>
