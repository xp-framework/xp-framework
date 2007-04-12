<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  uses(
    'peer.Socket',
    'util.cmd.Command',
    'peer.ProtocolException');
  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class MemcacheProtocol extends Command {
  
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
      $data && $this->_sock->write($data."\r\n");

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
     * Checks the Reply of the three store commands add, set, replace
     *
     * @param   string answer
     * @return  bool
     * @throws  lang.IllegalStateException
     */
    protected function checkStoreReply($answer) {
      if ($answer == "STORED\r\n") {
        return TRUE;
      } else if ($answer== "NOT_STORED\r\n") {
        return FALSE;
      } else {
        throw new IllegalStateException($answer);
        return FALSE;
      }    
    }
    
    /**
     * Adds a new entry as long as no entry with the same key
     * already exists
     *
     * @param   string key, 
     * @param   int flag, 
     * @param   string data, 
     * @param   int key,           
     * @return  bool
     */
    public function add($key, $flag, $data, $expirein= 3600) {
      $answer= $this->_cmd('add '.$key.' '.$flag.' '.$expirein.' '.strlen($data), $data);
      return $this->checkStoreReply($answer);
    }

    /**
     * Store this data
     *
     * @param   string key, 
     * @param   int flag, 
     * @param   string data, 
     * @param   int key,           
     * @return  bool
     */
    public function set($key, $flag, $data, $expirein= 3600) {
      $answer= $this->_cmd('set '.$key.' '.$flag.' '.$expirein.' '.strlen($data), $data);
      return $this->checkStoreReply($answer);      
    }

    /**
     * Replace an already existing entry
     *
     * @param   string key, 
     * @param   int flag, 
     * @param   string data, 
     * @param   int key,           
     * @return  bool
     */
    public function replace($key, $flag, $data, $expirein= 3600) {
      $answer= $this->_cmd('replace '.$key.' '.$flag.' '.$expirein.' '.strlen($data), $data);
      return $this->checkStoreReply($answer);
    }
    
    /**
     * Get an Item from the Memcache
     *
     * @param   string key
     * @return  string
     */
    public function get($key) {
      return $this->_cmd('get '.$key);
    }
    
    /**
     * Delete an item from the memcache
     *
     * @param   
     * @return  
     */
    public function delete($key, $blockfor) {
      $answer= $this->_cmd('delete '.$key.' '.$blockfor);
      if ($answer == "DELETED\r\n") {
        return TRUE;
      } else if ($answer== "NOT_FOUND\r\n") {
        return FALSE;
      } else {
        throw new IllegalStateException($answer);
        return FALSE;
      }        
      
    }    

    public function run(){
      var_dump($this->add('test1', '2343', 'END'));
      var_dump($this->get('test1'));
    }
  }
?>
