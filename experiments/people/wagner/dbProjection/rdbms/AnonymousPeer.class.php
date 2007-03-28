<?php
/* This class is part of the XP framework
 *
 * $Id: Peer.class.php 9322 2007-01-17 15:18:22Z kiesel $ 
 */

  uses('rdbms.Peer');
  uses('rdbms.Record');

  /**
   * AnonymousPeer
   *
   * @see      xp://rdbms.Peer
   * @purpose  Part of DataSet model
   */
  class AnonymousPeer extends Peer {

    protected static 
      $instance   = array();

    /**
     * Constructor
     *
     * @param   string identifier
     */
    protected function __construct($identifier) {
      $this->identifier= $identifier;
      $peer= Peer::getInstance($this->identifier);
      $this->table      = $peer->table;
      $this->connection = $peer->connection;
      $this->sequence   = $peer->sequence;
      $this->identity   = $peer->identity;
      $this->primary    = $peer->primary;
      $this->types      = $peer->types;
    }

    /**
     * Retrieve an instance by a given identifier
     *
     * @param   string identifier
     * @return  rdbms.Peer
     */
    public static function getInstance($identifier) {
      if (!isset(self::$instance[$identifier])) self::$instance[$identifier]= new self($identifier);
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
     * Returns a new DataSet object.
     *
     * @param   array record optional
     * @return  rdbms.Object
     */    
    public function newObject($record= array()) {
      return new Record($record);
    }
    
    /**
     * Returns an iterator for a select statement
     *
     * @param   rdbms.Criteria criteria
     * @return  rdbms.ResultIterator
     * @see     xp://rdbms.ResultIterator
     */
    public function iteratorFor($criteria) {
      return new ResultIterator(
        $criteria->executeSelect(ConnectionManager::getInstance()->getByHost($this->connection, 0), $this), 
        $this->newObject()
      );
    }

    /**
     * Inserts this object into the database
     *
     * @param   array values
     * @return  mixed identity value or NULL if not applicable
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function doInsert($values) {
      throw new SQLException(__CLASS__.' can not be written');
    }

    /**
     * Update this object in the database by specified criteria
     *
     * @param   array values
     * @param   rdbms.Criteria criteria
     * @return  int number of affected rows
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function doUpdate($values, $criteria) {
      throw new SQLException(__CLASS__.' can not be written');
    }

    /**
     * Delete this object from the database by specified criteria
     *
     * @param   rdbms.Criteria criteria
     * @return  int number of affected rows
     * @throws  rdbms.SQLException in case an error occurs
     */  
    public function doDelete($criteria) {
      throw new SQLException(__CLASS__.' can not be written');
    }
  }
?>
