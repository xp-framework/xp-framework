<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'rdbms.ResultIterator',
    'rdbms.ConnectionManager',
    'rdbms.Column',
    'rdbms.Record'
  );

  /**
   * Peer
   *
   * <code>
   *   // Retrieve Peer object for the specified dataset class
   *   $peer= Peer::forName('net.xp_framework.db.caffeine.XPNews');
   *   
   *   // select * from news where news_id < 100
   *   $news= $peer->doSelect(new Criteria(array('news_id', 100, LESS_THAN)));
   * </code>
   *
   * @test     xp://net.xp_framework.unittest.rdbms.DataSetTest
   * @see      xp://rdbms.DataSet
   * @purpose  Part of DataSet model
   */
  class Peer extends Object {
    protected static 
      $instance   = array();

    public
      $identifier = '',
      $table      = '',
      $connection = '',
      $sequence   = NULL,
      $identity   = NULL,
      $primary    = array(),
      $types      = array(),
      $relations  = array();

    /**
     * Constructor
     *
     * @param   string identifier
     */
    protected function __construct($identifier) {
      $this->identifier= $identifier;
    }

    /**
     * Set Identifier
     *
     * @param   string identifier
     */
    public function setIdentifier($identifier) {
      $this->identifier= $identifier;
    }

    /**
     * Set Table
     *
     * @param   string table
     */
    public function setTable($table) {
      $this->table= $table;
    }

    /**
     * Set Connection
     *
     * @param   mixed connection either a name or a DBConnection instance
     */
    public function setConnection($connection) {
      // If we are passed a DBConnection, set the conn member directly,
      // else store the name passed in - we will retrieve the connection
      // object later. The lazy loading semantics used here have to do with
      // the fact that this is called from the DataSet class' static 
      // initializers - when they are run, the connection manager may not
      // be set up yet!
      if ($connection instanceof DBConnection) {
        $this->conn= $connection;
      } else {
        $this->connection= $connection;
        $this->conn= NULL;
      }
    }

    /**
     * Get Connection
     *
     * @return  rdbms.DBConnection
     */
    public function getConnection() {
      if (!isset($this->conn)) {
        $this->conn= ConnectionManager::getInstance()->getByHost($this->connection, 0);
      }
      return $this->conn;
    }

    /**
     * Set Identity
     *
     * @param   string identity
     */
    public function setIdentity($identity) {
      $this->identity= $identity;
    }

    /**
     * Set Sequence
     *
     * @param   string sequence
     */
    public function setSequence($sequence) {
      $this->sequence= $sequence;
    }

    /**
     * Set Types
     *
     * @param   mixed[] types
     */
    public function setTypes($types) {
      $this->types= $types;
    }

    /**
     * Set Primary
     *
     * @param   mixed[] primary
     */
    public function setPrimary($primary) {
      $this->primary= $primary;
    }

    /**
     * Set relations
     *
     * @param   mixed[] relations
     */
    public function setRelations($relations) {
      $this->relations= $relations;
    }

    /**
     * Retrieve an instance by a given identifier
     *
     * @param   string identifier
     * @return  rdbms.Peer
     */
    public static function getInstance($identifier) {
      if (!isset(self::$instance[$identifier])) {
        self::$instance[$identifier]= new self($identifier);
      }
      return self::$instance[$identifier];
    }
      
    /**
     * Retrieve an instance by a given XP class name
     *
     * @param   string fully qualified class name
     * @return  rdbms.Peer
     */
    public static function forName($classname) {
      return self::getInstance(xp::reflect($classname));
    }

    /**
     * Retrieve an instance by a given instance
     *
     * @param   lang.Object instance
     * @return  rdbms.Peer
     */
    public static function forInstance($instance) {
      return self::getInstance(get_class($instance));
    }
    
    /**
     * Begins a transaction
     *
     * @param   rdbms.Transaction transaction
     * @return  rdbms.Transaction
     */
    public function begin(Transaction $transaction) {
      return $this->getConnection()->begin($transaction);
    }
    
    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
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
     * column factory
     *
     * @param   string name
     * @return  rdbms.Column
     * @throws  lang.IllegalArgumentException
     */
    public function column($name) {
      return new Column($this, $name);
    }
    
    /**
     * Get related peer by relation path array
     *
     * @param   string[] path
     * @return  rdbms.Peer
     * @throws  lang.IllegalArgumentException
     */
    public function getRelatedPeer(Array $path) {
      if (0 == sizeof($path)) return $this;
      $name= array_shift($path);
      if (!isset($this->relations[$name])) throw new IllegalArgumentException('relation '.$name.' does not exist for '.$this->identifier);
      return XPClass::forName($this->relations[$name]['classname'])->getMethod('getPeer')->invoke(NULL)->getRelatedPeer($path);
    }
    
    /**
     * Retrieve a number of objects from the database
     *
     * @param   rdbms.SQLExpressin criteria or statement
     * @param   int max default 0
     * @return  rdbms.Record[]
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function doSelect(SQLExpression $criteria, $max= 0) {
      $r= array();
      for ($i= 1, $it= $this->iteratorFor($criteria); $it->hasNext() && (!$max || $i <= $max); $i++) {
        $r[]= $it->next();
      }
      return $r;
    }

    /**
     * Returns an iterator for a select statement
     *
     * @param   rdbms.SQLExpression criteria or statement
     * @return  util.XPIterator
     * @see     xp://lang.XPIterator
     */
    public function iteratorFor(SQLExpression $criteria) {
      $jp= $criteria->isJoin() ? new JoinProcessor($this) : NULL;
      $rs= $criteria->executeSelect($this->getConnection(), $this, $jp);

      // if this is a projection, it does no matter if it's a join or not
      if ($criteria->isProjection()) return new ResultIterator($rs, 'Record');
      if ($criteria->isJoin())       return $jp->getJoinIterator($rs);
      return new ResultIterator($rs, $this->identifier);
    }

    /**
     * Returns a DataSet object for given associative array
     *
     * @param   array record
     * @return  rdbms.DataSet
     * @throws  lang.IllegalArgumentException
     */    
    public function objectFor($record) {
      if (array_keys($this->types) != array_keys($record)) {
        throw new IllegalArgumentException(
          'Record not compatible with '.$this->identifier.' class'
        );
      }
      return $this->newObject($record);
    }

    /**
     * Returns a new DataSet object.
     *
     * @param   array record optional
     * @return  rdbms.DataSet
     */    
    public function newObject($record= array()) {
      return new $this->identifier($record);
    }
    
    /**
     * Returns a new Record object.
     *
     * @param   array record optional
     * @return  rdbms.Record
     */    
    public function newRecord($record= array()) {
      return new Record($record);
    }
    
    /**
     * Inserts this object into the database
     *
     * @param   array values
     * @return  mixed identity value or NULL if not applicable
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function doInsert($values) {
      $id= NULL;
      $db= $this->getConnection();

      // Build the insert command
      $sql= $db->prepare(
        'into %c (%c) values (',
        $this->table,
        array_keys($values)
      );
      foreach (array_keys($values) as $key) {
        $sql.= $db->prepare($this->types[$key][0], $values[$key]).', ';
      }

      // Send it
      if ($db->insert('%c', substr($sql, 0, -2).')')) {

        // Fetch identity value if applicable.
        $this->identity && $id= $db->identity($this->sequence);
      }

      return $id;
    }

    /**
     * Update this object in the database by specified criteria
     *
     * @param   array values
     * @param   rdbms.SQLExpression criteria
     * @return  int number of affected rows
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function doUpdate($values, SQLExpression $criteria) {
      $db= $this->getConnection();

      // Build the update command
      $sql= '';
      foreach (array_keys($values) as $key) {
        $sql.= $db->prepare('%c = '.$this->types[$key][0], $key, $values[$key]).', ';
      }

      // Send it
      return $db->update(
        '%c set %c%c',
        $this->table,
        substr($sql, 0, -2),
        $criteria->toSQL($db, $this)
      );
    }

    /**
     * Delete this object from the database by specified criteria
     *
     * @param   rdbms.SQLExpression criteria
     * @return  int number of affected rows
     * @throws  rdbms.SQLException in case an error occurs
     */  
    public function doDelete(SQLExpression $criteria) {
      $db= $this->getConnection();

      // Send it
      return $db->delete(
        'from %c%c',
        $this->table,
        $criteria->toSQL($db, $this)
      );
    }
  }
?>
