<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  uses('rdbms.SQLFragment');

  /**
   * represents a table column
   * should be build via a dataset's factory Dataset::column(name)
   * 
   * <code>
   *   $col= Nmappoint::column("texture_id"); // where Nmappoint is a generated dataset class
   *   $criteria= Criteria::newInstance()->add(Restrictions::equal($col, 5);
   * </code>
   */
  class Column extends Object implements SQLFragment{
    
    private
      $peer= NULL,
      $type= '',
      $name= '';

    /**
     * Constructor
     *
     * @param   rdbms.Peer peer
     * @param   string name
     */
    public function __construct($peer, $name) {
      $this->peer= $peer;
      $this->name= $name;
      if (!isset($this->peer->types[$this->name])) throw new SQLStateException('field '.$this->name.' does not exist');
      $this->type= $this->peer->types[$this->name][0];
    }

    /**
     * Get type
     *
     * @return  string
     */
    public function getType() {
      return $this->type;
    }

    /**
     * Returns the fragment SQL
     *
     * @param   rdbms.DBConnection conn
     * @return  string
     * @throws  rdbms.SQLStateException
     */
    public function asSql(DBConnection $conn) {
      return $this->name;
    }

  }
?>
