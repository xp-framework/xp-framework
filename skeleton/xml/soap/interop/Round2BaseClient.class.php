<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xml.soap.SOAPClient', 'xml.soap.transport.SOAPHTTPTransport');

  /**
   * Standard Round2 Base test client
   *
   * @see      reference
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
     * @param   mixed argument
     * @return  boolean
     * @throws  xml.soap.SOAPFaultException
     */
    function identity($method, $argument) {
      try(); {
      
        // Invoke the given function
        $result= &$this->invoke($method, $argument);
        
      } if (catch ('SOAPFaultException', $e)) {
        return throw ($e);
      }
      
      if ($this->_iotrace) {
        $this->_iotrace->info('Method', $method, 'called with:', $argument);
        $this->_iotrace->info('Method', $method, 'returned', $result);
      }
      
      return ($result === $argument);
    }
  
    /**
     * echoString
     *
     * @access  public
     * @return  boolean match
     */
    function echoString() {
      return $this->identity('echoString', 'Hello World!');
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
        array('Hello Earth!', 'Hello Mars!')
      );
    }
    
    /**
     * echoIntegerArray
     *
     * @access  public
     * @return  boolean match
     */
    function echoInteger() {
      return $this->identity('echoInteger', 42);
    }
    
    /**
     * echoInteger
     *
     * @access  public
     * @return  boolean match
     */
    function echoIntegerArray() {
      return $this->identity('echoIntegerArray', array(42, 23));
    }

    /**
     * echoFloat
     *
     * @access  public
     * @return  boolean match
     */
    function echoFloat() {
      return $this->identity('echoFloat', 0.5);
    }
    
    /**
     * echoFloatArray
     *
     * @access  public
     * @return  boolean match
     */
    function echoFloatArray() {
      return $this->identity('echoFloatArray', array(0.5, 1.5, 45789234.45));
    }
    
    /**
     * echoStruct
     *
     * @access  public
     * @return  boolean match
     */
    function echoStruct() {
      return $this->identity(
        'echoStruct', array(
          'varString' => 'myString',
          'varInt'    => 23,
          'varFloat'  => 25.776
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
      return $this->identity('echoStructArray', array($s, $s));
    }
    
    /**
     * echoVoid
     *
     * @access  public
     * @return  boolean match
     */
    function echoVoid() {
      return $this->identity('echoVoid', NULL);
    }
  }
?>
