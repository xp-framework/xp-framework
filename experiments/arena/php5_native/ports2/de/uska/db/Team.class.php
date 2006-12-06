<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('rdbms.DataSet');
 
  /**
   * Class wrapper for table team, database uska
   * (Auto-generated on Sat, 09 Apr 2005 12:04:04 +0200 by alex)
   *
   * @purpose  Datasource accessor
   */
  class Team extends DataSet {
    public
      $team_id            = 0,
      $name               = '';

    /**
     * Static initializer
     *
     * @model   static
     * @access  public
     */
    public static function __static() { 
      with ($peer= &Team::getPeer()); {
        $peer->setTable('uska.team');
        $peer->setConnection('uskadb');
        $peer->setIdentity('team_id');
        $peer->setPrimary(array('team_id'));
        $peer->setTypes(array(
          'team_id'             => '%d',
          'name'                => '%s'
        ));
      }
    }  
  
    /**
     * Retrieve associated peer
     *
     * @access  public
     * @return  &rdbms.Peer
     */
    public function &getPeer() {
      return Peer::forName(__CLASS__);
    }
  
    /**
     * Gets an instance of this object by index "PRIMARY"
     *
     * @access  static
     * @param   int team_id
     * @return  &de.uska.db.Team object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function &getByTeam_id($team_id) {
      $peer= &Team::getPeer();
      return array_shift($peer->doSelect(new Criteria(array('team_id', $team_id, EQUAL))));
    }

    /**
     * Retrieves team_id
     *
     * @access  public
     * @return  int
     */
    public function getTeam_id() {
      return $this->team_id;
    }
      
    /**
     * Sets team_id
     *
     * @access  public
     * @param   int team_id
     * @return  int the previous value
     */
    public function setTeam_id($team_id) {
      return $this->_change('team_id', $team_id);
    }

    /**
     * Retrieves name
     *
     * @access  public
     * @return  string
     */
    public function getName() {
      return $this->name;
    }
      
    /**
     * Sets name
     *
     * @access  public
     * @param   string name
     * @return  string the previous value
     */
    public function setName($name) {
      return $this->_change('name', $name);
    }
  }
?>