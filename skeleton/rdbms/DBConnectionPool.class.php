<?php
  /* Connection-Pool
   *
   * $Id$
   */
   
  define('E_POOL_OBJECT_EXCEPTION',     0xFF12);
  
  class DBConnectionPool extends Object {
    var $pool;
    
    function DBConnectionPool($params= NULL) {
      parent::__construct($params);
      $this->pool= array();
    }
    
    function register(&$obj, $hostAlias= NULL, $userAlias= NULL) {
      $host= (NULL == $hostAlias) ? $obj->host : $hostAlias;
      $user= (NULL == $userAlias) ? $obj->user : $userAlias;
      
      if (isset($this->pool["$user@$host"])) return throw(
        E_POOL_OBJECT_EXCEPTION,
        $host.'/'.$user.'-combination already registered'
      );
      $this->pool["$user@$host"]= &$obj;
    }
    
    function &get($host, $user) {
      if (!isset($this->pool["$user@$host"])) return throw(
        E_POOL_OBJECT_EXCEPTION,
        $name.' not found'
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
    
    function __destruct() {
      parent::__destruct();
    }
  }
?>
