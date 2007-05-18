<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  uses(
    'peer.BSDSocket',
    'peer.ProtocolException',
    'util.log.Traceable'
  );
  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class MemcacheProtocol extends Object {
  
    public
      $_sock      = NULL,
      $timeout    = 0;
    
    protected
      $cat        = NULL;
  
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function __construct($server= '172.17.29.2', $port= 11211, $timeout= 3600) {
      $this->_sock= new BSDSocket($server, $port);
      $this->timeout= $timeout;
      $this->_sock->setOption(getprotobyname('tcp'), TCP_NODELAY, TRUE);
      $this->_sock->connect();
    }

    /**
     * Set a trace for debugging
     *
     * @param   &util.log.LogCategory cat
     */
    public function setTrace($cat) {
      $this->cat= $cat;
    }
    
    /**
     * Sends and receives commands on the socket
     *
     * @param   string command
     * @param   string data     
     * @return  mixed answer
     */
    protected function _cmd($command, $data= NULL) {
      if (!$this->_sock->isConnected()) {
        throw(new IllegalStateException('Not connected'));
      }

      // writes command to the socket. Data only if its available.
      $this->cat && $this->cat->debug('>>>', $command);
      $this->_sock->write($command."\r\n");
      $data && $this->cat && $this->cat->debug('>>>', $data);
      $data && $this->_sock->write($data."\r\n");
      
      $numberofargs= str_word_count($command);
      
      $this->cat->debug(substr($command, 0, 3));

      // If only one Value is requested
      if (substr($command, 0, 3) == 'get' && $numberofargs== 2) {
        $answer= $this->_getHelper();
        $answer && $this->_sock->readLine();
        
      //If more than one Value is requested
      } else if (substr($command, 0, 3) == 'get' && $numberofargs > 2) {
        while ($tempanswer= $this->_getHelper()) {
          $answer[]= $tempanswer;
        }

      // For single-line responses
      } else {
        $answer= '';
        while ("\n" != substr($answer, -1) && $buf= $this->_sock->read(0x1000)) {
          $answer.= $buf;
        }
      }
      
      $this->cat && $this->cat->debug('<<<', $answer);

      return $answer;
    }
    
    /**
     * Helps getting the multi-line responses
     * from a get-request
     *
     * @return  array
     */
    protected function _getHelper() { 
      // Split the result header and write it to an array
      $buf= $this->_sock->readLine();
      $this->cat && $this->cat->debug('<<<', $buf);

      $n= sscanf($buf, '%s %s %d %d', $type, $key, $flags, $size);
      
      if ($type== 'VALUE') {
        $answer['key']= $key;
        $answer['flags']= $flags;
        while (strlen($datatmp)< $size) {
          $datatmp.= $this->_sock->read(1024);
        }
        $answer['data']= $datatmp;
        $this->_sock->readLine();
        $this->_sock->readLine();
      } else if ($type== 'END') {
        return NULL;
      }
      return $answer;      
    }
        
    /**
     * Checks the reply of the three store commands add, set, replace
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
        throw new IllegalStateException('Illegal answer: '. $answer);
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
     * Get multiple Items from the Memcache
     *
     * @param   string key
     * @return  string
     */
    public function getMultiple() {
      $args= func_get_args();
      $key= implode(' ', $args);
      return $this->_cmd('get '.$key);
    }
    
    /**
     * Delete an item from the memcache
     * $blockfor sets the time the key will be not available
     *
     * @param   string key
     * @param   int blockfor
     * @return  bool
     */
    public function delete($key, $blockfor= 600) {
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

    /**
     * Decreases or increases a value on the server
     *
     * @param   string key
     * @param   int value
     * @return  bool
     */
    public function deincrease($key, $value) {
      ($value> 0) && $answer= $this->_cmd('incr '.$key.' '.$value);
      ($value< 0) && $answer= $this->_cmd('decr '.$key.' '.abs($value));
      if ($answer== "NOT_FOUND\r\n") {
        return FALSE;
      } else {
        return TRUE;
      }        
    }    
  }
?>
