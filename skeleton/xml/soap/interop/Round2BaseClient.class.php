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
    var
      $_iotrace=  NULL;

    /**
     * Sets the trace category for input/output trace.
     * This trace can be used to track what arguments
     * a function has been called and what the deserialized
     * result was.
     *
     * @access  public
     * @param   &util.log.LogCategory
     */
    function setInputOutputTrace(&$cat) {
      $this->_iotrace= &$cat;
    }

    /**
     * Invoke given method with given argument and
     * check whether the result matches the argument.
     *
     * @access  protected
     * @param   string method
     * @param   &xml.soap.Paramater argument
     * @return  boolean
     * @throws  xml.soap.SOAPFaultException
     */
    function identity($method, &$argument) {
      try(); {
        $result= &$this->invoke($method, $argument);
      } if (catch('SOAPFaultException', $e)) {
        return throw($e);
      }
      
      if ($this->_iotrace) {
        $this->_iotrace->info('Method', $method, 'called with:', $argument);
        $this->_iotrace->info('Method', $method, 'returned', $result);
      }
      
      return ($result === $argument->value);
    }
  
    /**
     * echoString
     *
     * @access  public
     * @return  boolean match
     */
    function echoString() {
      return $this->identity('echoString', new Parameter('inputString', 'Hello World!'));
    }
    
    /**
     * echoStringArray
     *
     * @access  public
     * @return  boolean match
     */
    function echoStringArray() {
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
    function echoInteger() {
      return $this->identity('echoInteger', new Parameter('inputInteger', 42));
    }
    
    /**
     * echoIntegerArray
     *
     * @access  public
     * @return  boolean match
     */
    function echoIntegerArray() {
      return $this->identity('echoIntegerArray', new Parameter('inputIntegerArray', array(42, 23)));
    }

    /**
     * echoFloat
     *
     * @access  public
     * @return  boolean match
     */
    function echoFloat() {
      return $this->identity('echoFloat', new Parameter('inputFloat', 0.5));
    }
    
    /**
     * echoFloatArray
     *
     * @access  public
     * @return  boolean match
     */
    function echoFloatArray() {
      return $this->identity('echoFloatArray', new Parameter('inputFloatArray', array(0.5, 1.5, 45789234.45)));
    }
    
    /**
     * echoStruct
     *
     * @access  public
     * @return  boolean match
     */
    function echoStruct() {
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
    function echoStructArray() {
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
    function echoVoid() {
      return $this->identity('echoVoid', new Parameter('inputVoid', NULL));
    }
  }
?>
