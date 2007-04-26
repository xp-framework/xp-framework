<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table workflow_return_code, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Workflow_return_code extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..workflow_return_code');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array('workflow_id', 'return_code'));
        $peer->setTypes(array(
          'description'         => array('%s', FieldType::VARCHAR, TRUE),
          'return_code'         => array('%d', FieldType::INT, FALSE),
          'workflow_id'         => array('%d', FieldType::NUMERIC, FALSE)
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
     * Gets an instance of this object by index "PK_WORKFLOW"
     * 
     * @param   int workflow_id
     * @param   int return_code
     * @return  de.schlund.db.methadon.Workflow_return_code entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByWorkflow_idReturn_code($workflow_id, $return_code) {
      return new self(array(
        'workflow_id'  => $workflow_id,
        'return_code'  => $return_code,
        '_loadCrit' => new Criteria(
          array('workflow_id', $workflow_id, EQUAL),
          array('return_code', $return_code, EQUAL)
        )
      ));
    }

    /**
     * Retrieves description
     *
     * @return  string
     */
    public function getDescription() {
      return $this->description;
    }
      
    /**
     * Sets description
     *
     * @param   string description
     * @return  string the previous value
     */
    public function setDescription($description) {
      return $this->_change('description', $description);
    }

    /**
     * Retrieves return_code
     *
     * @return  int
     */
    public function getReturn_code() {
      return $this->return_code;
    }
      
    /**
     * Sets return_code
     *
     * @param   int return_code
     * @return  int the previous value
     */
    public function setReturn_code($return_code) {
      return $this->_change('return_code', $return_code);
    }

    /**
     * Retrieves workflow_id
     *
     * @return  int
     */
    public function getWorkflow_id() {
      return $this->workflow_id;
    }
      
    /**
     * Sets workflow_id
     *
     * @param   int workflow_id
     * @return  int the previous value
     */
    public function setWorkflow_id($workflow_id) {
      return $this->_change('workflow_id', $workflow_id);
    }
  }
?>