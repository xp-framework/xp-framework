<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rmi.RMIObject');

  /**
   * RMI base
   *
   * Client code:
   * <code>
   *   uses('rmi.RMIObject', 'rmi.connector.SocketRMIConnector');
   *   
   *   $r= new RMIObject(new SocketRMIConnector(new Socket('localhost', 1061)));
   *   try(); {
   *     echo "1. Set value to 'hello world'\n";
   *     $r->value= 'hello world';
   *     echo "2. Get value\n";
   *     var_dump($r->value);
   *     echo "3. Invoke method hello()\n";
   *     var_dump($r->hello(1, 6.1, TRUE, array('a', 'b')));
   *   } if (catch('Exception', $e)) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   * </code>
   *
   * @see      xp://rmi.server.RMIServer
   * @ext      overload
   * @purpose  Base class
   */
  class RMIObject extends Object {
    public
      $connector= NULL;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   &rmi.RMIConnector connector
     */
    public function __construct(RMIConnector $connector) {
    
      // & missing intentionally, overloaded objects have problems with 
      // this! Adding an ampersand here results in "Fatal error: Cannot 
      // create references to/from string offsets nor overloaded objects"
      $this->connector= $connector;
      
    }
    
    /**
     * Destructor
     *
     * @access  public
     */
    public function __destruct() {
    
      // $this->connector->__destruct() results in this function being
      // called in an infinite loop. Seems to be a bug with overload
      call_user_func(array($this->connector, '__destruct()'));
      
    }

    /**
     * Member variable getter interceptor
     *
     * @access  magic
     * @param   string name
     * @param   &mixed value
     * @return  bool
     */
    public function __get($name, $value) {
      try {
        $value= $this->connector->getValue($this, $name);
      } catch (RMIException $e) {
        throw ($e);
      }
      return TRUE;
    }

    /**
     * Member variable setter interceptor
     *
     * @access  magic
     * @param   string name
     * @param   &mixed value
     * @return  bool
     */
    public function __set($name, $value) {
      try {
        $this->connector->setValue($this, $name, $value);
      } catch (RMIException $e) {
        throw ($e);
      }
      return TRUE;
    }
    
    /**
     * Method call interceptor
     *
     * @access  magic
     * @param   string name
     * @param   &array args
     * @param   &mixed return
     * @return  bool
     */
    public function __call($name, $args, $return) {
      try {
        $return= $this->connector->invokeMethod($this, $name, $args);
      } catch (RMIException $e) {
        throw ($e);
      }
      return TRUE;
    }
    
  } overload('RMIObject');
?>
