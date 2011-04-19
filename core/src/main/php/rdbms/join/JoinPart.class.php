<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'rdbms.join.JoinTable',
    'rdbms.join.JoinTableAttribute',
    'rdbms.join.JoinRelation',
    'util.collections.HashTable'
  );

  /**
   * Helper class for rdbms.join.JoinProcessor.
   * A JoinPart contains a JoinTable and multiple JoinTableAttributes.
   * You can link a JoinPart to an other JoinPart by self::addRelative().
   * Wit self::getJoinRelations can fetch all conditions to do a join with all relatives.
   *
   * @test .  xp_framework.unittest.rdbms.JoinPartTest
   * @see     xp://rdbms.join.JoinProcessor
   * @see     xp://rdbms.join.JoinTable
   * @see     xp://rdbms.join.JoinTableAttributes
   * @purpose rdbms.join
   */
  class JoinPart extends Object {
    public
      $peer=      NULL;

    protected
      $table=     NULL,
      $id=        '',
      $role=      '',
      $relatives= array(),
      $pkeys=     array(),
      $attrs=     array();

    /**
     * Constructor
     * id is also used as alias.
     *
     * @param   string id
     * @param   rdbms.Peer peer
     */
    public function __construct($id, Peer $peer) {
      $this->id= $id;
      $this->peer= $peer;
      $this->pkeys= array();
      $this->attrs= array();

      foreach (array_keys($peer->types) as $attr) $this->attrs[$attr]= new JoinTableAttribute($this->id, $attr);

      foreach ($peer->primary as $key) {
        if (empty($key)) continue;
        $this->pkeys[$key]= new JoinTableAttribute($this->id, $key);
      }
      if (empty($this->pkeys) && !empty($peer->identity)) $this->pkeys[$peer->identity]= new JoinTableAttribute($this->id, $peer->identity);
      if (empty($this->pkeys)) $this->pkeys= $this->attrs;

      $this->table= new JoinTable($this->peer->table, $this->id);
    }

    /**
     * get column names for the aggregated peer and all futher joined tables (relatives)
     *
     * @return  string[]
     */
    public function getAttributes() {
      $r= array();
      foreach ($this->attrs as $attr) $r[]= $attr->toSqlString();
      foreach ($this->relatives as $tjp) foreach ($tjp->getAttributes() as $attr) $r[]= $attr;
      return $r;
    }

    /**
     * get table names for the aggregated peer and all futher joined tables (relatives)
     *
     * @return  rdbms.join.JoinTable
     */
    public function getTable() {
      return $this->table;
    }

    /**
     * get conditional statements to join the aggregated peer and its next JoinPart
     * and for all futher relatives
     *
     * @return  rdbms.join.JoinRelation[]
     */
    public function getJoinRelations() {
      $r= array();
      foreach ($this->relatives as $tjp) {
        $conditions= array();
        foreach ($this->peer->relations[$tjp->role]['key'] as $source => $target) $conditions[]= $this->id.'.'.$source.' = '.$tjp->id.'.'.$target;
        $rel= new JoinRelation($this->table, $tjp->getTable());
        $rel->setConditions($conditions);
        $r[]= $rel;
        foreach ($tjp->getJoinRelations() as $joinConditions) $r[]= $joinConditions;
      }
      return $r;
    }

    /**
     * build an object of a single database row for the aggregated peer
     *
     * @param   rdbms.join.JoinExtractable caller
     * @param   string[] record
     * @param   string role
     */
    public function extract(JoinExtractable $caller, array $record, $role) {
      $key= $this->key($record);
      if (FALSE === $key) return;
      if (!$caller->hasCachedObj($role, $key)) $caller->setCachedObj($role, $key, $this->peer->objectFor($this->attributes($record)));
      $obj= $caller->getCachedObj($role, $key);
      foreach ($this->relatives as $tjp) {
        $obj->markAsCached($tjp->role);
        $tjp->extract($obj, $record, $tjp->role);
      }
    }

    /**
     * add relatives
     *
     * @param   lang.Object relatives
     * @param   string role
     */
    public function addRelative(JoinPart $relatives, $role) {
      $relatives->role= $role;
      $this->relatives[$role]= $relatives;
    }

    /**
     * form a key from a record
     *
     * @param   string[] record
     * @return  string
     */
    private function key(array $record) {
      $key= '';
      foreach ($this->pkeys as $pKey) {
        if (!isset($record[$pKey->getAlias()])) return FALSE;
        $key.= '#'.$record[$pKey->getAlias()];
      }
      return $key;
    }

    /**
     * get all property values for the aggregated peer from a database row
     *
     * @param   string[] record
     * @return  string[]
     */
    private function attributes(array $record) {
      $recordchunk= array();
      foreach ($this->attrs as $attr) $recordchunk[$attr->getAlias()]= $record[$attr->getAlias()];
      return array_combine(array_keys($this->peer->types), $recordchunk);
    }
  }
?>
