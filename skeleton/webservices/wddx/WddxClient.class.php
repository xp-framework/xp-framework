<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('webservices.wddx.WddxMessage');

  /**
   * This is a WDDX client; WDDX is a remote procedure call
   * protocol that uses XML as the message format.
   *
   * <code>
   *   uses('webservices.wddx.WddxClient');
   *   $c= new WddxClient(new WddxHttpTransport('http://wddx.xp-framework.net/server/'));
   *   
   *   try {
   *     $res= $c->invoke(5, 3);
   *   } catch(XPException $e) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   *
   *   echo $res;
   * </code>
   *
   * @ext      xml
   * @see      http://openwddx.org
   * @purpose  Generic WDDX Client base class
   */
  class WddxClient extends Object {
    public
      $transport  = NULL,
      $message    = NULL,
      $answer     = NULL;

    /**
     * Constructor.
     *
     * @param   webservices.wddx.transport.WddxTransport transport
     */
    public function __construct($transport) {
      $this->transport= $transport;
    }
    
    /**
     * Set trace for debugging
     *
     * @param   util.log.LogCategory cat
     */
    public function setTrace($cat) {
      $this->transport->setTrace($cat);
    }
    
    /**
     * Invoke a method on a XML-RPC server
     *
     * @param   string method
     * @param   mixed vars
     * @return  mixed answer
     * @throws  lang.IllegalArgumentException
     */
    public function invoke() {
      if (!$this->transport instanceof WddxHttpTransport) throw new IllegalArgumentException(
        'Transport must be a webservices.wddx.transport.WddxHttpTransport'
      );
    
      $args= func_get_args();
      
      $this->message= new WddxMessage();
      $this->message->create();
      $this->message->setData($args);
      
      // Send
      if (FALSE == ($response= $this->transport->send($this->message))) return FALSE;
      
      // Retrieve response
      if (FALSE == ($this->answer= $this->transport->retrieve($response))) return FALSE;
      
      $data= $this->answer->getData();
      return $data;
    }
  }
?>
