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
     * echoInteger
     *
     * @access  public
     * @return  boolean match
     */
    function echoInteger() {
      return $this->identity('echoInteger', 42);
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
  }
?>
