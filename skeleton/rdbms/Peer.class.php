<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Peer
   *
   * <code>
   *   // Retrieve Peer object for the specified dataset class
   *   $peer= &Peer::forName('net.xp-framework.db.caffeine.XPNews');
   *   
   *   // select * from news where news_id < 100
   *   $news= $peer->doSelect(new Criteria(array('news_id', 100, LESS_THAN)));
   * </code>
   *
   * @see      reference
   * @purpose  purpose
   */
  class Peer extends Object {
    var
      $identifier = '',
      $table      = '',
      $connection = '',
      $identity   = NULL,
      $primary    = array(),
      $types      = array();
    
    /**
     * Constructor
     *
     * @access  protected
     * @param   string identifier
     */
    function __construct($identifier) {
      $this->identifier= $identifier;
    }

    /**
     * Set Identifier
     *
     * @access  public
     * @param   string identifier
     */
    function setIdentifier($identifier) {
      $this->identifier= $identifier;
    }

    /**
     * Get Identifier
     *
     * @access  public
     * @return  string
     */
    function getIdentifier() {
      return $this->identifier;
    }

    /**
     * Set Table
     *
     * @access  public
     * @param   string table
     */
    function setTable($table) {
      $this->table= $table;
    }

    /**
     * Get Table
     *
     * @access  public
     * @return  string
     */
    function getTable() {
      return $this->table;
    }

    /**
     * Set Connection
     *
     * @access  public
     * @param   string connection
     */
    function setConnection($connection) {
      $this->connection= $connection;
    }

    /**
     * Get Connection
     *
     * @access  public
     * @return  string
     */
    function getConnection() {
      return $this->connection;
    }

    /**
     * Set Identity
     *
     * @access  public
     * @param   string identity
     */
    function setIdentity($identity) {
      $this->identity= $identity;
    }

    /**
     * Get Identity
     *
     * @access  public
     * @return  string
     */
    function getIdentity() {
      return $this->identity;
    }

    /**
     * Set Types
     *
     * @access  public
     * @param   mixed[] types
     */
    function setTypes($types) {
      $this->types= $types;
    }

    /**
     * Set Primary
     *
     * @access  public
     * @param   mixed[] primary
     */
    function setPrimary($primary) {
      $this->primary= $primary;
    }

    /**
     * Get Primary
     *
     * @access  public
     * @return  mixed[]
     */
    function getPrimary() {
      return $this->primary;
    }

    /**
     * Get Types
     *
     * @access  public
     * @return  mixed[]
     */
    function getTypes() {
      return $this->types;
    }

    /**
     * Retrieve an instance by a given identifier
     *
     * @access  protected
     * @param   string identifier
     * @return  &rdbms.Peer
     */
    function &getInstance($identifier) {
      static $instance= array();
      
      if (!isset($instance[$identifier])) {
        $instance[$identifier]= &new Peer($identifier);
      }
      return $instance[$identifier];
    }
      
    /**
     * Retrieve an instance by a given XP class name
     *
     * @access  protected
     * @param   string fully qualified class name
     * @return  &rdbms.Peer
     */
    function &forName($classname) {
      return Peer::getInstance(xp::reflect($classname));
    }

    /**
     * Retrieve an instance by a given instance
     *
     * @access  protected
     * @param   &lang.Object instance
     * @return  &rdbms.Peer
     */
    function &forInstance(&$instance) {
      return Peer::getInstance(get_class($instance));
    }
    
    /**
     * Creates a string representation of this object
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return sprintf(
        '%s@(%s accessing %s on connection "%s"){%s}', 
        $this->getClassName(),
        $this->identifier,
        $this->table,
        $this->connection,
        substr(var_export($this->types, 1), 7, -1)
      );
    }
    
    /**
     * Retrieve a number of objects from the database
     *
     * @model   final
     * @access  public
     * @param   &rdbms.Criteria criteria
     * @param   int max default 0
     * @return  rdbms.DataSet[]
     * @throws  rdbms.SQLException in case an error occurs
     */
    function doSelect(&$criteria, $max= 0) {
      $cm= &ConnectionManager::getInstance();  
      try(); {
        $db= &$cm->getByHost($this->connection, 0);
        $q= &$db->query(
          'select %c from %c%c',
          array_keys($this->types),
          $this->table,
          $criteria->toSQL($db, $this->types)
        );
      } if (catch('SQLException', $e)) {
        return throw($e);
      }
      
      $r= array();
      for ($i= 1; $record= $q->next(); $i++) {
        if ($max && $i > $max) break;
        $r[]= &new $this->identifier($record);
      }
      return $r;
    }
    
    /**
     * Inserts this object into the database
     *
     * @model   final
     * @access  public
     * @param   array values
     * @return  mixed identity value or NULL if not applicable
     * @throws  rdbms.SQLException in case an error occurs
     */
    function doInsert($values) {
      $id= NULL;
      $cm= &ConnectionManager::getInstance();
      try(); {
        $db= &$cm->getByHost($this->connection, 0);

        // Build the insert command
        $sql= $db->prepare(
          'into %c (%c) values (',
          $this->table,
          array_keys($values)
        );
        foreach (array_keys($values) as $key) {
          $sql.= $db->prepare($this->types[$key], $values[$key]).', ';
        }
        
        // Send it
        if ($db->insert(substr($sql, 0, -2).')')) {

          // Fetch identity value if applicable.
          $this->identity && $id= $db->identity();
        }
      } if (catch('SQLException', $e)) {
        return throw($e);
      }

      return $id;
    }

    /**
     * Update this object in the database by specified criteria
     *
     * @model   final
     * @access  public
     * @param   array values
     * @param   &rdbms.Criteria criteria
     * @return  int number of affected rows
     * @throws  rdbms.SQLException in case an error occurs
     */
    function doUpdate($values, &$criteria) {
      $cm= &ConnectionManager::getInstance();  
      try(); {
        $db= &$cm->getByHost($this->connection, 0);

        // Build the update command
        $sql= '';
        foreach (array_keys($values) as $key) {
          $sql.= $db->prepare('%c = '.$this->types[$key], $key, $values[$key]).', ';
        }

        // Send it
        $affected= $db->update(
          '%c set %c%c',
          $this->table,
          substr($sql, 0, -2),
          $criteria->toSQL($db, $this->types)
        );
      } if (catch('SQLException', $e)) {
        return throw($e);
      }

      return $affected;
    }

    /**
     * Delete this object from the database by specified criteria
     *
     * @model   final
     * @access  public
     * @param   &rdbms.Criteria criteria
     * @return  int number of affected rows
     * @throws  rdbms.SQLException in case an error occurs
     */  
    function doDelete(&$criteria) {
      $cm= &ConnectionManager::getInstance();  
      try(); {
        $db= &$cm->getByHost($this->connection, 0);

        // Send it
        $affected= $db->delete(
          'from %c%c',
          $this->table,
          $criteria->toSQL($db, $this->types)
        );
      } if (catch('SQLException', $e)) {
        return throw($e);
      }

      return $affected;
    }
  }
?>
