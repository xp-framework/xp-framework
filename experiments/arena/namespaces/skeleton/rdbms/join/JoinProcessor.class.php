<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  namespace rdbms::join;
  uses(
    'rdbms.SQLDialect',
    'rdbms.join.JoinIterator',
    'rdbms.join.JoinPart',
    'rdbms.join.Fetchmode'
  );

  /**
   * Collect data to do join selects.
   * The JoinProcessor takes as input a start peer (__construct) an a list of
   * fetchmodes (setFetchmodes). With this information it calculates
   * the attribute, location and criteria parts of a query.
   *
   * @test net.xp_framework.unittest.rdbms.JoinProcessorTest
   * @see     xp://rdbms.join.Fetchmode
   * @purpose rdbms.join
   */
  class JoinProcessor extends lang::Object {
    const SEPARATOR= '->';
    const FIRST= 'start';
  
    private
      $joinparts=   array();
    
    private static
      $isJoinContext= 0;
    
    public
      $joinpart=   NULL;
    
    /**
     * Constructor
     * 
     *
     * @param   rdbms.Peer peer
     */
    public function __construct(rdbms::Peer $peer) {
      $this->joinpart= new JoinPart(JoinProcessor::FIRST, $peer);
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
    public function getJoinIterator(rdbms::ResultSet $rs) {
      return new JoinIterator($this, $rs);
    }

    /**
     * set array with fetchmodes
     * the path is stored as array keys
     *
     * @param   rdbms.join.Fetchmode[] fetchmodes
     * @throws  lang.IllegalArgumentException
     */
    public function setFetchmodes(Array $fetchmodes) {
      if (0 == sizeof(array_keys($fetchmodes, 'join'))) throw new lang::IllegalArgumentException('fetchmodes must contain at least one join element');
      foreach ($fetchmodes as $path => $fetchmode) {
        if ('join' != $fetchmode) continue;
        $this->transformFetchmode(explode(self::SEPARATOR, $path), $this->joinpart);
      }
    }

    /**
     * get the key for a path
     *
     * @param   string[] path
     * @return  string
     */
    public static function pathToKey(Array $path) {
      if (0 == sizeof($path)) return  self::FIRST;
      return implode('_', $path);
    }

    /**
     * test if join context is set
     * hack is necessary, because of the first tables elements in a join
     *
     * @return  boolean
     */
    public static function isJoinContext() {
      return (bool)self::$isJoinContext;
    }

    /**
     * switch joinContext
     *
     */
    public function leaveJoinContext() {
      self::$isJoinContext++;
    }

    /**
     * switch joinContext
     *
     */
    public function enterJoinContext() {
      self::$isJoinContext--;
    }

    /**
     * go through the fetechmode array and transform it to a tree
     * collect JoinPart objects
     *
     * @param   string[] path
     * @param   rdbms.join.JoinPart current joinpart
     * @param   string[] curpath
     * @throws  lang.IllegalArgumentException
     */
    private function transformFetchmode(Array $path, JoinPart $sjp, $curpath= array()) {
      if (0 == sizeof($path)) return;
      $role= array_shift($path);

      if (!isset($sjp->peer->relations[$role])) {
        throw new lang::IllegalArgumentException($role.': no such role for '.$sjp->peer->identifier.' - try one of '.implode(', ', array_keys($sjp->peer->relations)));
      }
      
      $curpath[]= $role;
      $key= self::pathToKey($curpath);
      if (!isset($this->joinparts[$key])) $this->joinparts[$key]= new JoinPart($key, lang::XPClass::forName($sjp->peer->relations[$role]['classname'])->getMethod('getPeer')->invoke(NULL));
      $sjp->addRelative($this->joinparts[$key], $role);

      $this->transformFetchmode($path, $this->joinparts[$key], $curpath);
    }
    
  }
?>
