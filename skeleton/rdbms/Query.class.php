<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  uses(
    'lang.IllegalArgumentException',
    'lang.IllegalStateException',
    'rdbms.Criteria'
  );

  /**
   * store complete queries with criteria, method and peer
   *
   * @purpose  rdbms
   */
  class Query extends Object {
    const INSERT= 'insert';
    const UPDATE= 'update';
    const SELECT= 'select';
    const DELETE= 'delete';
   
    private
      $mode    = '',
      $criteria= NULL,
      $peer    = NULL;
    
    /**
     * set mode
     *
     * @param  string mode 
     * @throws lang.IllegalArgumentException
     */
    public function setMode($mode) {
      if (!in_array($mode, array(
        self::INSERT,
        self::UPDATE,
        self::SELECT,
        self::DELETE,
      ))) throw new IllegalArgumentException('mode must be in self::INSERT, self::UPDATE, self::SELECT or self::DELETE');
      $this->mode= $mode;
    }
    
    /**
     * get mode
     *
     * @param  string mode 
     */
    public function getMode() {
      return $this->mode;
    }
    
    /**
     * set criteria
     *
     * @param  rdbms.Criteria criteria
     */
    public function setCriteria($criteria) {
      $this->criteria= $criteria;
    }
    
    /**
     * get criteria
     *
     * @return  rdbms.Criteria
     */
    public function getCriteria() {
      return $this->criteria;
    }
    
    /**
     * set peer
     *
     * @param  rdbms.Peer peer
     */
    public function setPeer($peer) {
      $this->peer= $peer;
    }
    
    /**
     * get peer
     *
     * @return rdbms.Peer
     */
    public function getPeer() {
      return $this->peer;
    }
    
    /**
     * get connection for peer
     *
     * @return rdbms.DBConnection
     */
    public function getConnection() {
      return $this->peer->getConnection();
    }
    
    /**
     * make copy with added restriction restriction
     *
     * @param  rdbms.Criteria criterion
     * @return rdbms.Query
     */
    public function withRestriction(Criterion $criterion) {
      $q= clone($this);
      if (is_null($q->getCriteria())) $q->setCriteria(new Criteria());
      $q->getCriteria()->add($criterion);
      return $q;
    }
    
    /**
     * execute query
     *
     * @param  mixed[] values
     * @return mixed
     * @throws lang.IllegalStateException
     */
    public function execute($values) {
      if (strlen($this->mode) == 0) throw new IllegalStateException('no mode set');
      if (is_null($this->peer))     throw new IllegalStateException('no peer set');
      if (is_null($this->criteria)) $this->criteria= new Criteria();
      switch ($this->mode) {
        case self::INSERT:
        if ($this->criteria->isJoin) throw new IllegalStateException("can't insert into joins");
        return $this->peer->doInsert($values);

        case self::UPDATE:
        if ($this->criteria->isJoin) throw new IllegalStateException("can't update into joins");
        return $this->peer->doUpdate($values, $this->criteria);

        case self::SELECT:
        return $this->peer->doSelect($this->criteria, $values);

        case self::DELETE:
        if($this->criteria->isJoin) throw new IllegalStateException("can't delete from joins");
        return $this->peer->doDelete($this->criteria);
      }
    }
    
  }
?>
