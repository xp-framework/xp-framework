<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table workflow, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Workflow extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..workflow');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array('workflow_id'));
        $peer->setTypes(array(
          'description'         => array('%s', FieldType::VARCHAR, TRUE),
          'workflow_id'         => array('%d', FieldType::NUMERIC, FALSE),
          'maintainer_id'       => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE)
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
     * @return  de.schlund.db.methadon.Workflow entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByWorkflow_id($workflow_id) {
      return new self(array(
        'workflow_id'  => $workflow_id,
        '_loadCrit' => new Criteria(array('workflow_id', $workflow_id, EQUAL))
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

    /**
     * Retrieves maintainer_id
     *
     * @return  int
     */
    public function getMaintainer_id() {
      return $this->maintainer_id;
    }
      
    /**
     * Sets maintainer_id
     *
     * @param   int maintainer_id
     * @return  int the previous value
     */
    public function setMaintainer_id($maintainer_id) {
      return $this->_change('maintainer_id', $maintainer_id);
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
     * Retrieves the Person entity
     * referenced by person_id=>maintainer_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getMaintainer() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getMaintainer_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves an array of all Message entities referencing
     * this entity by workflow_id=>workflow_id
     *
     * @return  de.schlund.db.methadon.Message[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getMessageWorkflowList() {
      return XPClass::forName('de.schlund.db.methadon.Message')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('workflow_id', $this->getWorkflow_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Message entities referencing
     * this entity by workflow_id=>workflow_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Message>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getMessageWorkflowIterator() {
      return XPClass::forName('de.schlund.db.methadon.Message')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('workflow_id', $this->getWorkflow_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Workflow_context_change entities referencing
     * this entity by to_workflow_id=>workflow_id
     *
     * @return  de.schlund.db.methadon.Workflow_context_change[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getWorkflow_context_changeTo_workflowList() {
      return XPClass::forName('de.schlund.db.methadon.Workflow_context_change')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('to_workflow_id', $this->getWorkflow_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Workflow_context_change entities referencing
     * this entity by to_workflow_id=>workflow_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Workflow_context_change>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getWorkflow_context_changeTo_workflowIterator() {
      return XPClass::forName('de.schlund.db.methadon.Workflow_context_change')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('to_workflow_id', $this->getWorkflow_id(), EQUAL)
      ));
    }
  }
?>