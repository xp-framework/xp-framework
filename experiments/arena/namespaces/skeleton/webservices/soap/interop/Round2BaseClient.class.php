<?php
/* This class is part of the XP framework
 *
 * $Id: Round2BaseClient.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace webservices::soap::interop;

  uses('webservices.soap.SoapDriver');

  /**
   * Standard Round2 Base test client
   *
   * @see      http://interop.xp-framework.net/
   * @purpose  Perform Round2 base tests
   */
  class Round2BaseClient extends lang::Object {
    public
      $client   = NULL,
      $_iotrace = NULL;

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function __construct($endpoint, $uri) {
      $this->client= webservices::soap::SoapDriver::getInstance()->forEndpoint($endpoint, $uri);
    }

    /**
     * Sets the trace category for input/output trace.
     * This trace can be used to track what arguments
     * a function has been called and what the deserialized
     * result was.
     *
     * @param   util.log.LogCategory cat
     */
    public function setInputOutputTrace($cat) {
      $this->_iotrace= $cat;
    }

    /**
     * Invoke given method with given argument and
     * check whether the result matches the argument.
     *
     * @param   string method
     * @param   webservices.soap.Parameter argument
     * @return  boolean
     * @throws  webservices.soap.SOAPFaultException
     */
    public function identity($method, $argument) {
      $result= $this->client->invoke($method, $argument);
      
      if ($argument instanceof )
        $cmp= $argument->value;
      else 
        $cmp= $argument;
      
      if ($this->_iotrace) {
        $this->_iotrace->info('Method', $method, 'called with:', ::xp::typeOf($cmp), $cmp, var_export($cmp, 1));
        $this->_iotrace->info('Method', $method, 'returned', ::xp::typeOf($result), $result, var_export($result, 1));
      }
      
      if ($cmp instanceof lang::Generic) {
        return $cmp->equals($result);
      }
      
      return ($result === $cmp);
    }
  
    /**
     * echoString
     *
     * @return  boolean match
     */
    public function echoString() {
      return $this->identity('echoString', new ('inputString', 'Hello World!'));
    }
    
    /**
     * echoStringArray
     *
     * @return  boolean match
     */
    public function echoStringArray() {
      return $this->identity(
        'echoStringArray',
        new ('inputStringArray', array('Hello Earth!', 'Hello Mars!'))
      );
    }
    
    /**
     * echoInteger
     *
     * @return  boolean match
     */
    public function echoInteger() {
      return $this->identity('echoInteger', new ('inputInteger', 42));
    }
    
    /**
     * echoIntegerArray
     *
     * @return  boolean match
     */
    public function echoIntegerArray() {
      return $this->identity('echoIntegerArray', new ('inputIntegerArray', array(42, 23)));
    }

    /**
     * echoFloat
     *
     * @return  boolean match
     */
    public function echoFloat() {
      return $this->identity('echoFloat', new ('inputFloat', 0.5));
    }
    
    /**
     * echoFloatArray
     *
     * @return  boolean match
     */
    public function echoFloatArray() {
      return $this->identity('echoFloatArray', new ('inputFloatArray', array(0.5, 1.5, 45789234.45)));
    }
    
    /**
     * echoStruct
     *
     * @return  boolean match
     */
    public function echoStruct() {
      return $this->identity(
        'echoStruct', new ('inputStruct', array(
          'varString' => 'myString',
          'varInt'    => 23,
          'varFloat'  => 25.776
        )
      ));
    }

    /**
     * echoStructArray
     *
     * @return  boolean match
     */
    public function echoStructArray() {
      $s= array(
        'varString' => 'myString',
        'varInt'    => 23,
        'varFloat'  => 25.776
      );
      return $this->identity('echoStructArray', new ('inputStructArray', array($s, $s)));
    }
    
    /**
     * echoVoid
     *
     * @return  boolean match
     */
    public function echoVoid() {
      return $this->identity('echoVoid', new ('inputVoid', NULL));
    }
    
    /**
     * echoBase64
     *
     * @return  boolean match
     */
    public function echoBase64() {
      return $this->identity('echoBase64', 
        new ('inputBase64', new ("\0\1\127")
      ));
    }
    
    /**
     * echoHexBinary
     *
     * @return  boolean match
     */
    public function echoHexBinary() {
      return $this->identity('echoHexBinary',
        new ('inputHexBinary', new ("\0\1\127")
      ));
    }
    
    /**
     * echoDate
     *
     * @return  boolean match
     */
    public function echoDate() {
      return $this->identity('echoDate', new ('inputDate', util::Date::now()));
    }
    
    /**
     * echoDecimal
     *
     * @return  boolean match
     */
    public function echoDecimal() {
      return $this->identity('echoDecimal', new ('inputDecimal', 0.5005));
    }
    
    /**
     * echoBoolean
     *
     * @return  boolean match
     */
    public function echoBoolean() {
      return $this->identity('echoBoolean', new ('inputBoolean', TRUE));
    }
  }
?>
