<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table bug_binaryhistory_matrix, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Bug_binaryhistory_matrix extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..bug_binaryhistory_matrix');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array(''));
        $peer->setTypes(array(
          'bughistory_id'       => array('%d', FieldType::NUMERIC, FALSE),
          'binary_id'           => array('%d', FieldType::NUMERIC, FALSE)
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
     * Retrieves bughistory_id
     *
     * @return  int
     */
    public function getBughistory_id() {
      return $this->bughistory_id;
    }
      
    /**
     * Sets bughistory_id
     *
     * @param   int bughistory_id
     * @return  int the previous value
     */
    public function setBughistory_id($bughistory_id) {
      return $this->_change('bughistory_id', $bughistory_id);
    }

    /**
     * Retrieves binary_id
     *
     * @return  int
     */
    public function getBinary_id() {
      return $this->binary_id;
    }
      
    /**
     * Sets binary_id
     *
     * @param   int binary_id
     * @return  int the previous value
     */
    public function setBinary_id($binary_id) {
      return $this->_change('binary_id', $binary_id);
    }

    /**
     * Retrieves the Binary entity
     * referenced by binary_id=>binary_id
     *
     * @return  de.schlund.db.methadon.Binary entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBinary() {
      $r= XPClass::forName('de.schlund.db.methadon.Binary')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('binary_id', $this->getBinary_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves the Bug_history entity
     * referenced by bughistory_id=>bughistory_id
     *
     * @return  de.schlund.db.methadon.Bug_history entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBughistory() {
      $r= XPClass::forName('de.schlund.db.methadon.Bug_history')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bughistory_id', $this->getBughistory_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>