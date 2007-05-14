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
   * collect data for join selects
   *
   */
  class JoinPart extends Object {
    public
      $peer=      NULL;
    protected
      $id=        '',
      $role=      '',
      $relatives= array(),
      $pkeys=     array(),
      $attrs=     array();

    /**
     * Constructor
     *
     * @param   string id
     */
    public function __construct($id) {
      $this->id= $id;
    }

    /**
     * get column names for the aggregated peer and all futher join tables
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
     * get table names for the aggregated peer and all futher join tables
     *
     * @return  rdbms.join.JoinTable[]
     */
    public function getTables() {
      $r= array(new JoinTable($this->peer->table, $this->id));
      foreach ($this->relatives as $tjp) foreach ($tjp->getTables() as $table) $r[]= $table;
      return $r;
    }
    
    /**
     * get conditional statements to join the aggregated peer and its next JoinPart
     * and for all futher relatives
     *
     * @return  string[]
     */
    public function getJoinConditions() {
      $r= array();
      foreach ($this->relatives as $tjp) {
        foreach ($this->peer->constraints[$tjp->role]['key'] as $source => $target) $r[$this->id.'#'.$tjp->getId()][]= $this->id.'.'.$source.' = '.$tjp->getId().'.'.$target;
        foreach ($tjp->getJoinConditions() as $id => $joinConditions) $r[$id]= $joinConditions;
      }
      return $r;
    }
    
    /**
     * build an object of a single database row for the aggregated peer
     *
     * @param   lang.Object X
     * @param   string callback function to register an object in X
     * @param   string callback function to ask for the existance of an object in X
     * @param   string callback function to get an object out of X
     * @param   string[] database record
     */
    public function extract($caller, $register_callback, $exist_callback, $get_callback, Array $record) {
      $k= $this->getKey($record);
      if (FALSE === $k) return;
      if (!$caller->{$exist_callback}($k)) $caller->{$register_callback}($k, $this->peer->objectFor($this->attributes($record)));
      $obj= $caller->{$get_callback}($k);
      foreach ($this->relatives as $tjp) {
        $obj->_cacheMark($this->relNameTo($tjp));
        $tjp->extract($obj, '_cacheAdd'.$this->relNameTo($tjp), '_cacheHas'.$this->relNameTo($tjp), '_cacheGet'.$this->relNameTo($tjp), $record);
      }
    }
    
    /**
     * Get id
     *
     * @return  string
     */
    public function getId() {
      return $this->id;
    }

    /**
     * Set relatives
     *
     * @param   lang.Object relatives
     */
    public function addRelation(JoinPart $relatives) {
      $this->relatives[]= $relatives;
    }

    /**
     * Set targetPeer
     *
     * @param   lang.Object peer
     */
    public function setPeer(Peer $peer) {
      $this->peer= $peer;
      $this->pkeys= array();
      $this->attrs= array();
      foreach ($this->peer->primary as $key) $this->pkeys[]= new JoinTableAttribute($this->id, $key);
      foreach (array_keys($this->peer->types) as $attr) $this->attrs[]= new JoinTableAttribute($this->id, $key);
    }

    /**
     * Get targetPeer
     *
     * @return  lang.Object
     */
    public function getPeer() {
      return $this->peer;
    }

    /**
     * Set role
     *
     * @param   string role
     */
    public function setRole($role) {
      $this->role= $role;
    }

    /**
     * Get role
     *
     * @return  string
     */
    public function getRole() {
      return $this->role;
    }

    /**
     * form a key from a record
     *
     * @param   string[]
     * @return  string
     */
    public function getKey(Array $record) {
      $keys= array_values(array_intersect_key($record, $this->pkeys));
      if (is_null($keys[0])) return FALSE;
      return implode('#', $keys);
    }
    
    /**
     * get the name of a constraint (role) to an other JoinPart
     *
     * @param   rdbms.join.JoinPart
     * @return  string
     */
    private function relNameTo(JoinPart $tjp) {
      return $tjp->role;
    }
    
    /**
     * get all property values for the aggregated peer from a database row
     *
     * @param   string[]
     * @return  string[]
     */
    private function attributes(Array $record) {
      return array_combine(array_keys($this->peer->types), array_intersect_key($record, $this->attrs));
    }
    
  }
?>
