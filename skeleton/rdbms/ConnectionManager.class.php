<?php
  /* Connection-Manager
   *
   * $Id$
   */
   
  define('E_POOL_OBJECT_EXCEPTION',     0xFF12);
  
  class ConnectionManager extends Object {
    var $pool;
    var $_ref= 0;
    
    function __construct($params= NULL) {
      Object::__construct($params);
      $this->pool= array();
    }
    
    /**
     * Instanz des Connection-Managers zurückgeben
     * 
     * @see SingleTon#getInstance
     */
    function &getInstance() {
      static $ConnectionManager__instance;
      
      if (!isset($ConnectionManager__instance)) {
        LOG::info("ConnectionManager::Creating new...");
        $ConnectionManager__instance= new ConnectionManager();
      }
      $ConnectionManager__instance->_ref++;
      LOG::info('ConnectionManager::ref= '.$ConnectionManager__instance->_ref);
      return $ConnectionManager__instance;
    }
    
    function register($obj, $hostAlias= NULL, $userAlias= NULL) {
      $host= (NULL == $hostAlias) ? $obj->host : $hostAlias;
      $user= (NULL == $userAlias) ? $obj->user : $userAlias;
      LOG::info('ConnectionManager::register ['.$user.'@'.$host.']');
      if (isset($this->pool["$user@$host"])) return throw(
        E_POOL_OBJECT_EXCEPTION,
        $host.'/'.$user.'-combination already registered'
      );
      $this->pool["$user@$host"]= $obj;
      return $obj;
    }
    
    function &get($host, $user) {
      if (!isset($this->pool["$user@$host"])) return throw(
        E_POOL_OBJECT_EXCEPTION,
        'no connections for '.$user.'@'.$host
      );
      return $this->pool["$user@$host"];
    }
    
    function &getByHost($hostName, $num= -1) {
      $results= array();
      foreach (array_keys($this->pool) as $id) {
        list ($user, $host)= explode('@', $id);
        if ($hostName == $host) $results[]= &$this->pool[$id];
      }
      if (sizeof($results)< 1) return throw(
        E_POOL_OBJECT_EXCEPTION,
        'no connections for host '.$hostName
      );
      return ($num < 0) ? $results : $results[$num];
    }
  }
?>
