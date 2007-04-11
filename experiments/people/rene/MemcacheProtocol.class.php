<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  uses('peer.Socket', 'peer.ProtocolException');
  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class MemcacheProtocol extends Object {
  
    public
      $_sock= NULL,
      $timeout= 0;
  
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function __construct($server= '172.17.29.45', $port= 11211, $timeout= 3600) {
      $this->_sock= new Socket($server, $port);
      $this->timeout= $timeout;
      $this->_sock->connect();
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    protected function _cmd($command, $data= NULL) {
      if (!$this->_sock->isConnected()) {
        throw(new IllegalStateException('Not connected'));
      }    

      $this->_sock->write($command."\r\n");
      if ($data) { 
        $this->_sock->write($data."\r\n");
      }

      if (substr($command, 0, 3) == 'get') {
        $answer= '';
        while ("END\r\n" != substr($answer, -5) && $buf= $this->_sock->readLine()) {
          $answer.= $buf."\r\n";
        }
      } else {
        $answer= '';
        while ("\n" != substr($answer, -1) && $buf= $this->_sock->read(0x1000)) {
          $answer.= $buf;
        }
      }

      return $answer;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function add($key, $flag, $data, $expirein= 3600) {
      return $this->_cmd('add '.$key.' '.$flag.' '.$expirein.' '.strlen($data), $data);
    }

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function set($key, $flag, $data, $expirein= 3600) {
      return $this->_cmd('set '.$key.' '.$flag.' '.$expirein.' '.strlen($data), $data);
    }

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function replace($key, $flag, $data, $expirein= 3600) {
      return $this->_cmd('replace '.$key.' '.$flag.' '.$expirein.' '.strlen($data), $data);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function get($key) {
      return $this->_cmd('get '.$key);
    }


    public function run(){
      var_dump($this->get('teste'));
    }
  }
?>
