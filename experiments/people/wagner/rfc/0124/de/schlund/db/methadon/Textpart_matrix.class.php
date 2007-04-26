<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table textpart_matrix, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Textpart_matrix extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..textpart_matrix');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array('textpart_id', 'project_id'));
        $peer->setTypes(array(
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'textpart_id'         => array('%d', FieldType::NUMERIC, FALSE),
          'project_id'          => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE)
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
     * Gets an instance of this object by index "PK_TEXTPART_MATRIX"
     * 
     * @param   int textpart_id
     * @param   int project_id
     * @return  de.schlund.db.methadon.Textpart_matrix entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByTextpart_idProject_id($textpart_id, $project_id) {
      return new self(array(
        'textpart_id'  => $textpart_id,
        'project_id'  => $project_id,
        '_loadCrit' => new Criteria(
          array('textpart_id', $textpart_id, EQUAL),
          array('project_id', $project_id, EQUAL)
        )
      ));
    }

    /**
     * Retrieves changedby
     *
     * @return  string
     */
    public function getChangedby() {
      return $this->changedby;
    }
      
    /**
     * Sets changedby
     *
     * @param   string changedby
     * @return  string the previous value
     */
    public function setChangedby($changedby) {
      return $this->_change('changedby', $changedby);
    }

    /**
     * Retrieves textpart_id
     *
     * @return  int
     */
    public function getTextpart_id() {
      return $this->textpart_id;
    }
      
    /**
     * Sets textpart_id
     *
     * @param   int textpart_id
     * @return  int the previous value
     */
    public function setTextpart_id($textpart_id) {
      return $this->_change('textpart_id', $textpart_id);
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
     * Retrieves bz_id
     *
     * @return  int
     */
    public function getBz_id() {
      return $this->bz_id;
    }
      
    /**
     * Sets bz_id
     *
     * @param   int bz_id
     * @return  int the previous value
     */
    public function setBz_id($bz_id) {
      return $this->_change('bz_id', $bz_id);
    }

    /**
     * Retrieves lastchange
     *
     * @return  util.Date
     */
    public function getLastchange() {
      return $this->lastchange;
    }
      
    /**
     * Sets lastchange
     *
     * @param   util.Date lastchange
     * @return  util.Date the previous value
     */
    public function setLastchange($lastchange) {
      return $this->_change('lastchange', $lastchange);
    }

    /**
     * Retrieves the Bearbeitungszustand entity
     * referenced by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Bearbeitungszustand entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBz() {
      $r= XPClass::forName('de.schlund.db.methadon.Bearbeitungszustand')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
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
     * Retrieves the Textpart entity
     * referenced by textpart_id=>textpart_id
     *
     * @return  de.schlund.db.methadon.Textpart entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTextpart() {
      $r= XPClass::forName('de.schlund.db.methadon.Textpart')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('textpart_id', $this->getTextpart_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>