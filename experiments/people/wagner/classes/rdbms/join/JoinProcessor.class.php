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
   * @test net.xp_framework.unittest.rdbms.JoinProcessorTest
   */
  class JoinProcessor extends Object {
    private
      $uid=  0;

    public
      $joinpart=   NULL;
    
    /**
     * Constructor
     *
     * @param   rdbms.Peer peer
     */
    public function __construct(Peer $peer) {
      $this->joinpart= new JoinPart('t'.$this->uid++, $peer);
    }
    
    /**
     * get join tables with join conditions
     *
     * @return  string
     */
    public function getJoinString() {
      $dialect= $this->joinpart->peer->getConnection()->getFormatter()->dialect;
      return $dialect->makeJoinBy($this->joinpart->getJoinRelations());
    }
    
    /**
     * get all attributs of a join
     *
     * @return  string[]
     */
    public function getAttributeString() {
      return implode(', ', $this->joinpart->getAttributes());
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
     * set array with fetchmodes
     * the path is stored as array keys
     *
     * @param   rdbms.join.FetchMode[] fetchmodes
     * @throws  lang.IllegalArgumentException
     */
    public function setFetchModes(Array $fetchmodes) {
      if (0 == sizeOf(array_keys($fetchmodes, 'join'))) throw new IllegalArgumentException('fetchmodes must contain at least one join element');
      $this->transformFetchmode($fetchmodes, $this->joinpart);
    }

    /**
     * go through the fetechmode array and transform it to a tree
     * collect JoinPart objects
     *
     * @param   rdbms.join.FetchMode[] fetchmodes
     * @param   JoinPart sjp joinPart for the first table
     * @throws  lang.IllegalArgumentException
     */
    private function transformFetchmode(Array $fetchmodes, JoinPart $sjp) {
      foreach ($fetchmodes as $path => $fetchmode) {
        if ('join' != $fetchmode) continue;
        if (0 == strlen($path))   continue;
        list($role, $n_path)= explode('.', $path, 2);

        if ((!$class= $sjp->peer->constraints[$role]['classname']) || (!$sjp->peer->constraints[$role]['key'])) {
          throw new IllegalArgumentException($role.': no such role for '.$sjp->peer->identifier.' - try one of '.implode(', ', array_keys($sjp->peer->constraints)));
        }

        $jp= new JoinPart('t'.$this->uid++, XPClass::forName($class)->getMethod('getPeer')->invoke());
        $sjp->addRelative($jp, $role);

        $this->transformFetchmode(array($n_path => $fetchmode), $jp);
      }
    }
    
  }
?>
