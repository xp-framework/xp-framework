<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  
  uses('lang.ElementNotFoundException');
  
  /**
   * ConnectionManager
   *
   * @purpose  Hold connections to databases
   */
  class ConnectionManager extends Object {
    var 
      $pool= array();
    
    /**
     * Return the ConnectionManager's instance
     * 
     * @model   static
     * @access  public
     * @return  &rdbms.ConnectionManager
     */
    function &getInstance() {
      static $__instance;
      
      if (!isset($__instance)) $__instance= new ConnectionManager();
      return $__instance;
    }
    
    /**
     * Configure this ConnectionManager
     *
     * A sample configuration file:
     * <pre>
     * [caffeine]
     * reflect=rdbms.sybase.SPSybase
     * host=gurke
     * user=news
     * pass=enieffac
     * db=CAFFEINE
     * </pre>
     *
     * @access  public
     * @param   &util.Properties properties
     */
    function configure(&$properties) {
      $section= $properties->getFirstSection();
      do {
        $defines= $properties->readSection($section);
        try(); {
          $c= ClassLoader::loadClass($defines['reflect']);
        } if (catch('Exception', $e)) {
          return throw(
            $e->type, 
            'ConnectionManager::couldn\'t use '.$defines['reflect'].'::'.$e->message
          );
        }

        unset($defines['reflect']);
        if (FALSE !== ($p= strpos($section, '.'))) $section= substr($section, 0, $p);
        $this->register(new $c($defines), $section);
      } while ($section= $properties->getNextSection());
    }
    
    /**
     * Register a connection
     *
     * @param   &lang.Object obj A connection object
     * @return  &lang.Object obj The connection object registered
     * @param   string hostAlias default NULL
     * @param   string userAlias default NULL
     */
    function &register(&$obj, $hostAlias= NULL, $userAlias= NULL) {
      $host= (NULL == $hostAlias) ? $obj->host : $hostAlias;
      $user= (NULL == $userAlias) ? $obj->user : $userAlias;
      
      if (!isset($this->pool["$user@$host"])) {
        $this->pool["$user@$host"]= &$obj;
      }
      
      return $obj;
    }
    
    /**
     * Return a database connection object by host and user
     *
     * @param   string host
     * @param   string user
     * @return  &lang.Object
     * @throws  ElementNotFoundException in case there's no connection under these names
     */
    function &get($host, $user) {
      if (!isset($this->pool["$user@$host"])) {
        return throw(new ElementNotFoundException('no connections registered for '.$user.'@'.$host));
      }
      return $this->pool["$user@$host"];
    }
    
    /**
     * Return one or more connections by host
     *
     * @param   string hostName
     * @param   int num default -1 offset, -1 for all
     * @return  &lang.Object
     */
    function &getByHost($hostName, $num= -1) {
      $results= array();
      foreach (array_keys($this->pool) as $id) {
        list ($user, $host)= explode('@', $id);
        if ($hostName == $host) $results[]= &$this->pool[$id];
      }
      if (sizeof($results) < 1) {
        return throw(new ElementNotFoundException('no connections registered for '.$hostName));
      }
      
      if ($num < 0) {
        return $results;
      }
      return $results[$num];
    }
  }
?>
