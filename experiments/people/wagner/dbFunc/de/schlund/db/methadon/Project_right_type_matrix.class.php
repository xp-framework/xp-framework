<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table project_right_type_matrix, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Project_right_type_matrix extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..project_right_type_matrix');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array(''));
        $peer->setTypes(array(
          'project_id'          => array('%d', FieldType::NUMERIC, FALSE),
          'right_type_id'       => array('%d', FieldType::NUMERIC, FALSE)
        ));
      }
    }  

    function __get($name) {
      $this->load();
      return $this->get($name);
    }

    function __sleep() {
      $this->load();
      return array_merge(array_keys(self::getPeer()->types), array('_new', '_changed'));
    }

    /**
     * force loading this entity from database
     *
     */
    public function load() {
      if ($this->_isLoaded) return;
      $this->_isLoaded= true;
      $e= self::getPeer()->doSelect($this->_loadCrit);
      if (!$e) return;
      foreach (array_keys(self::getPeer()->types) as $p) {
        if (isset($this->{$p})) continue;
        $this->{$p}= $e[0]->$p;
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
     * Retrieves project_id
     *
     * @return  int
     */
    public function getProject_id() {
      return $this->project_id;
    }
      
    /**
     * Sets project_id
     *
     * @param   int project_id
     * @return  int the previous value
     */
    public function setProject_id($project_id) {
      return $this->_change('project_id', $project_id);
    }

    /**
     * Retrieves right_type_id
     *
     * @return  int
     */
    public function getRight_type_id() {
      return $this->right_type_id;
    }
      
    /**
     * Sets right_type_id
     *
     * @param   int right_type_id
     * @return  int the previous value
     */
    public function setRight_type_id($right_type_id) {
      return $this->_change('right_type_id', $right_type_id);
    }

    /**
     * Retrieves the Project entity
     * referenced by project_id=>project_id
     *
     * @return  de.schlund.db.methadon.Project entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getProject() {
      $r= XPClass::forName('de.schlund.db.methadon.Project')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('project_id', $this->getProject_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves the Right_type entity
     * referenced by right_type_id=>right_type_id
     *
     * @return  de.schlund.db.methadon.Right_type entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getRight_type() {
      $r= XPClass::forName('de.schlund.db.methadon.Right_type')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('right_type_id', $this->getRight_type_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>