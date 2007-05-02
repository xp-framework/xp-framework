<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
uses(
  'rdbms.SQLDialect',
  'rdbms.join.JoinIterator',
  'rdbms.join.JoinPart',
  'rdbms.join.FetchMode'
);

  /**
   * collect data to do join selects
   *
   */
  class JoinProcessor extends Object {
    private
      $uid=  0;
    public
      $joinpart=   NULL;
    
    /**
     * Constructor
     *
     * @param   rdbms.Peer $peer
     */
    public function __construct(Peer $peer) {
      $this->joinpart= new JoinPart('t'.$this->uid++);
      $this->joinpart->setPeer($peer);
    }
    
    /**
     * set array with fetchmodes
     * the path is stored as array keys
     *
     * @param   rdbms.join.FetchMode[]
     */
    public function setFetchMode(Array $fetchmodes) {
      $this->transformFetchmode($fetchmodes, $this->joinpart);
    }

    /**
     * get join tables with join conditions
     *
     * @return  string[]
     */
    public function getJoin() {
      $dialect= SQLDialect::getDialect(ConnectionManager::getInstance()->getByHost($this->joinpart->peer->connection, 0));
      return $dialect->makeJoinBy($this->getTables(), $this->getJoinConditions());
    }
    
    /**
     * get all attributs of a join
     *
     * @return  string[]
     */
    public function getAttributes() {
      return $this->joinpart->getAttributes();
    }
    
    /**
     * transform a record to its related objects
     *
     * @param   rdbms.ResultSet rs
     * @return  rdbms.join.JoinIterator
     */
    public function getJoinIterator(ResultSet $rs) {
      return new JoinIterator($this, $rs);
    }

    /**
     * get joined tables
     *
     * @return  string[]
     */
    private function getTables() {
      return $this->joinpart->getTables();
    }
    
    /**
     * get join conditions
     *
     * @return  string[]
     */
    private function getJoinConditions() {
      return $this->joinpart->getJoinConditions();
    }

    /**
     * go through the fetechmode array and transform it to a tree
     * collect JoinPart objects
     *
     * @param   rdbms.join.FetchMode[]
     * @param   JoinPart joinPart for the first table
     * @throws  lang.IllegalArgumentException
     */
    private function transformFetchmode(Array $fetchmodes, JoinPart $sjp) {
      foreach ($fetchmodes as $path => $fetchmode) {
        if ('join' != $fetchmode) continue;
        if (0 == strlen($path))   continue;
        list($role, $n_path)= explode('.', $path, 2);

        if (
          (!$class= $sjp->peer->constraints[$role]['classname'])
          || (!$sjp->peer->constraints[$role]['key'])
        ) throw new IllegalArgumentException($role.': no such role for '.$sjp->peer->identifier.' - try one of '.implode(', ', array_keys($sjp->peer->constraints)));
          
        $jp= new JoinPart('t'.$this->uid++);
        $jp->setRole($role);
        $jp->setPeer(XPClass::forName($class)->getMethod('getPeer')->invoke());

        $sjp->addRelation($jp);
        $this->transformFetchmode(array($n_path => $fetchmode), $jp);
      }
    }
    
  }
?>
