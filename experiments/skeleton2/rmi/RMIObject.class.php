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
      $this->connector= $connector;
    }
    
    /**
     * Destructor
     *
     * @access  public
     */
    public function __destruct() {
      unset($this->connector);
    }

    /**
     * Member variable getter interceptor
     *
     * @access  magic
     * @param   string name
     * @return  mixed
     */
    public function __get($name) {
      return $this->connector->getValue($this, $name);
    }

    /**
     * Member variable setter interceptor
     *
     * @access  magic
     * @param   string name
     * @param   mixed value
     * @return  mixed
     */
    public function __set($name, $value) {
      return $this->connector->setValue($this, $name, $value);
    }
    
    /**
     * Method call interceptor
     *
     * @access  magic
     * @param   string name
     * @param   array args
     * @return  mixed
     */
    public function __call($name, $args) {
      return $this->connector->invokeMethod($this, $name, $args);
    }
  }
?>
