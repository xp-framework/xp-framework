<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xml.soap.SOAPClient', 'xml.soap.transport.SOAPHTTPTransport');

  /**
   * Standard Round2 Base test client
   *
   * @see      http://interop.xp-framework.net/
   * @purpose  Perform Round2 base tests
   */
  class Round2BaseClient extends SOAPClient {
    public
      $_iotrace=  NULL;

    /**
     * Sets the trace category for input/output trace.
     * This trace can be used to track what arguments
     * a function has been called and what the deserialized
     * result was.
     *
     * @access  public
     * @param   &util.log.LogCategory cat
     */
    public function setInputOutputTrace(&$cat) {
      $this->_iotrace= &$cat;
    }

    /**
     * Invoke given method with given argument and
     * check whether the result matches the argument.
     *
     * @access  protected
     * @param   string method
     * @param   &xml.soap.Parameter argument
     * @return  boolean
     * @throws  xml.soap.SOAPFaultException
     */
    public function identity($method, &$argument) {
      try {
        $result= &$this->invoke($method, $argument);
      } catch (SOAPFaultException $e) {
        throw($e);
      }
      
      if (is('xml.soap.Parameter', $argument)) 
        $cmp= &$argument->value;
      else 
        $cmp= &$argument;
      
      if ($this->_iotrace) {
        $this->_iotrace->info('Method', $method, 'called with:', xp::typeOf($cmp), $cmp, var_export($cmp, 1));
        $this->_iotrace->info('Method', $method, 'returned', xp::typeOf($result), $result, var_export($result, 1));
      }
      
      if (is('lang.Generic', $cmp) && is('lang.Generic', $result)) {
        return $cmp->equals($result);
      }
      
      return ($result === $cmp);
    }
  
    /**
     * echoString
     *
     * @access  public
     * @return  boolean match
     */
    public function echoString() {
      return $this->identity('echoString', new Parameter('inputString', 'Hello World!'));
    }
    
    /**
     * echoStringArray
     *
     * @access  public
     * @return  boolean match
     */
    public function echoStringArray() {
      return $this->identity(
        'echoStringArray',
        new Parameter('inputStringArray', array('Hello Earth!', 'Hello Mars!'))
      );
    }
    
    /**
     * echoInteger
     *
     * @access  public
     * @return  boolean match
     */
    public function echoInteger() {
      return $this->identity('echoInteger', new Parameter('inputInteger', 42));
    }
    
    /**
     * echoIntegerArray
     *
     * @access  public
     * @return  boolean match
     */
    public function echoIntegerArray() {
      return $this->identity('echoIntegerArray', new Parameter('inputIntegerArray', array(42, 23)));
    }

    /**
     * echoFloat
     *
     * @access  public
     * @return  boolean match
     */
    public function echoFloat() {
      return $this->identity('echoFloat', new Parameter('inputFloat', 0.5));
    }
    
    /**
     * echoFloatArray
     *
     * @access  public
     * @return  boolean match
     */
    public function echoFloatArray() {
      return $this->identity('echoFloatArray', new Parameter('inputFloatArray', array(0.5, 1.5, 45789234.45)));
    }
    
    /**
     * echoStruct
     *
     * @access  public
     * @return  boolean match
     */
    public function echoStruct() {
      return $this->identity(
        'echoStruct', new Parameter('inputStruct', array(
          'varString' => 'myString',
          'varInt'    => 23,
          'varFloat'  => 25.776
        )
      ));
    }

    /**
     * echoStructArray
     *
     * @access  public
     * @return  boolean match
     */
    public function echoStructArray() {
      $s= array(
        'varString' => 'myString',
        'varInt'    => 23,
        'varFloat'  => 25.776
      );
      return $this->identity('echoStructArray', new Parameter('inputStructArray', array($s, $s)));
    }
    
    /**
     * echoVoid
     *
     * @access  public
     * @return  boolean match
     */
    public function echoVoid() {
      return $this->identity('echoVoid', new Parameter('inputVoid', NULL));
    }
    
    /**
     * echoBase64
     *
     * @access  public
     * @return  boolean match
     */
    public function echoBase64() {
      return $this->identity('echoBase64', 
        new Parameter('inputBase64', new SOAPBase64Binary("\0\1\127")
      ));
    }
    
    /**
     * echoHexBinary
     *
     * @access  public
     * @return  boolean match
     */
    public function echoHexBinary() {
      return $this->identity('echoHexBinary',
        new Parameter('inputHexBinary', new SOAPHexBinary("\0\1\127")
      ));
    }
    
    /**
     * echoDate
     *
     * @access  public
     * @return  boolean match
     */
    public function echoDate() {
      return $this->identity('echoDate', new Parameter('inputDate', Date::now()));
    }
    
    /**
     * echoDecimal
     *
     * @access  public
     * @return  boolean match
     */
    public function echoDecimal() {
      return $this->identity('echoDecimal', new Parameter('inputDecimal', 0.5005));
    }
    
    /**
     * echoBoolean
     *
     * @access  public
     * @return  boolean match
     */
    public function echoBoolean() {
      return $this->identity('echoBoolean', new Parameter('inputBoolean', TRUE));
    }
  }
?>
