<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('rdbms.DataSet', 'util.HashmapIterator');

  /**
   * Class wrapper for table team, database uska
   * (Auto-generated on Sat, 23 Jun 2007 16:52:13 +0200 by Alex)
   *
   * @purpose  Datasource accessor
   */
  class Team extends DataSet {
    public
      $team_id            = 0,
      $name               = '';
  
    protected
      $cache= array(
      );

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('uska.team');
        $peer->setConnection('uska');
        $peer->setIdentity('team_id');
        $peer->setPrimary(array('team_id'));
        $peer->setTypes(array(
          'team_id'             => array('%d', FieldType::INT, FALSE),
          'name'                => array('%s', FieldType::VARCHAR, FALSE)
        ));
        $peer->setRelations(array(
        ));
      }
    }  

    /**
     * Retrieve associated peer
     *
     * @return  rdbms.Peer
     */
    public static function getPeer() {
      return Peer::forName(__CLASS__);
    }

    /**
     * column factory
     *
     * @param   string name
     * @return  rdbms.Column
     * @throws  lang.IllegalArumentException
     */
    public static function column($name) {
      return Peer::forName(__CLASS__)->column($name);
    }
  
    /**
     * Gets an instance of this object by index "PRIMARY"
     * 
     * @param   int team_id
     * @return  de.uska.db.Team entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByTeam_id($team_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('team_id', $team_id, EQUAL)));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves team_id
     *
     * @return  int
     */
    public function getTeam_id() {
      return $this->team_id;
    }
      
    /**
     * Sets team_id
     *
     * @param   int team_id
     * @return  int the previous value
     */
    public function setTeam_id($team_id) {
      return $this->_change('team_id', $team_id);
    }

    /**
     * Retrieves name
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }
      
    /**
     * Sets name
     *
     * @param   string name
     * @return  string the previous value
     */
    public function setName($name) {
      return $this->_change('name', $name);
    }
  }
?>