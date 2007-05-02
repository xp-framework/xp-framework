<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

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
      $objs=      array(),
      $relations= array(),
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
      foreach (array_keys($this->peer->types) as $attribute) $r[]= $this->id.'.'.$attribute.' as '.$this->id.'_'.$attribute;
      foreach ($this->relations as $tjp) foreach ($tjp->getAttributes() as $attribute) $r[]= $attribute;
      return $r;
    }
    
    /**
     * get table names for the aggregated peer and all futher join tables
     *
     * @return  string[]
     */
    public function getTables() {
      $r= array($this->id => $this->peer->table.' as '.$this->id);
      foreach ($this->relations as $tjp) foreach ($tjp->getTables() as $id => $table) $r[$id]= $table;
      return $r;
    }
    
    /**
     * get conditional statements to join the aggregated peer and its next JoinPart
     * and for all futher relations
     *
     * @return  string[]
     */
    public function getJoinConditions() {
      $r= array();
      foreach ($this->relations as $tjp) {
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
      foreach ($this->relations as $tjp) {
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
     * Set relations
     *
     * @param   lang.Object relations
     */
    public function addRelation(JoinPart $relations) {
      $this->relations[]= $relations;
    }

    /**
     * Set targetPeer
     *
     * @param   lang.Object peer
     */
    public function setPeer(Peer $peer) {
      $this->peer= $peer;
      foreach ($this->peer->primary as $key) $this->pkeys[$this->id.'_'.$key]= '';
      foreach (array_keys($this->peer->types) as $attr) $this->attrs[$this->id.'_'.$attr]= '';
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
