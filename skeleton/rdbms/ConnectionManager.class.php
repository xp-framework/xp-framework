<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */
  
  uses(
    'lang.ElementNotFoundException',
    'util.LOG'
  );
  
  /**
   * ConnectionManager
   *
   * @access    static
   */
  class ConnectionManager extends Object {
    var 
      $pool= array(),
      $_ref= 0;
    
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
    
    /**
     * Konfigurieren
     *
     * @param   util.Properties properties Ein Objekt der Properties-Klasse
     */
    function configure($properties) {
      $section= $properties->getFirstSection();
      do {
        $defines= $properties->readSection($section);
        try(); {
          uses($defines['reflect']);
        } if (catch('Exception', $e)) {
          return throw(
            $e->type, 
            'ConnectionManager::couldn\'t use '.$defines['reflect'].'::'.$e->message
          );
        }

        $reflect= substr($defines['reflect'], strrpos($defines['reflect'], '.')+ 1);
        unset($defines['reflect']);
        
        // In den Pool mit aufnehmen
        $this->register(
          new $reflect($defines),
          $section
        );
      } while ($section= $properties->getNextSection());
    }
    
    /**
     * Ein Datenbank-Objekt registrieren - über die Aliase kann später einfacher zugegriffen werden
     *
     * @param   mixed obj Ein Datenbank-Objekt
     * @param   string hostAlias default NULL ein Alias für den Hostnamen, ansonsten der Hostname des Datenbank-Objekts
     * @param   string userAlias default NULL ein Alias für den Usernamen, ansonsten der Username des Datenbank-Objekts
     */
    function register($obj, $hostAlias= NULL, $userAlias= NULL) {
      $host= (NULL == $hostAlias) ? $obj->host : $hostAlias;
      $user= (NULL == $userAlias) ? $obj->user : $userAlias;
      LOG::info('ConnectionManager::register ['.$user.'@'.$host.']');
      if (!isset($this->pool["$user@$host"])) {
        $this->pool["$user@$host"]= $obj;
        return $obj;
      }
    }
    
    /**
     * Ein Datenbank-Objekt zurückgeben
     *
     * @param   string host Der Hostname
     * @param   string user Der Username
     * @return  mixed Datenbank-Objekt
     */
    function &get($host, $user) {
      if (!isset($this->pool["$user@$host"])) {
        return throw(new ElementNotFoundException('no connections registered for '.$user.'@'.$host));
      }
      return $this->pool["$user@$host"];
    }
    
    /**
     * Ein oder mehrere Datenbank-Objekt(e) zu einem Hostnamen zurückgeben
     *
     * @param   string hostName Der Hostname
     * @param   int num default -1 Den num'ten Eintrag, -1 = alle
     * @return  mixed Datenbank-Objekt(e)
     */
    function &getByHost($hostName, $num= -1) {
      $results= array();
      foreach (array_keys($this->pool) as $id) {
        list ($user, $host)= explode('@', $id);
        if ($hostName == $host) $results[]= &$this->pool[$id];
      }
      if (sizeof($results)< 1) {
        return throw(new ElementNotFoundException('no connections registered for '.$hostName));
      }
      return ($num < 0) ? $results : $results[$num];
    }
  }
?>
