<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table workflow_context_change, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Workflow_context_change extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..workflow_context_change');
        $peer->setConnection('sybintern');
        $peer->setIdentity('wcc_ident');
        $peer->setPrimary(array(''));
        $peer->setTypes(array(
          'set_workflow_state'  => array('%d', FieldType::INT, FALSE),
          'wcc_ident'           => array('%d', FieldType::NUMERIC, FALSE),
          'message_id'          => array('%d', FieldType::NUMERIC, FALSE),
          'to_workflow_id'      => array('%d', FieldType::NUMERIC, FALSE)
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
     * Retrieves set_workflow_state
     *
     * @return  int
     */
    public function getSet_workflow_state() {
      return $this->set_workflow_state;
    }
      
    /**
     * Sets set_workflow_state
     *
     * @param   int set_workflow_state
     * @return  int the previous value
     */
    public function setSet_workflow_state($set_workflow_state) {
      return $this->_change('set_workflow_state', $set_workflow_state);
    }

    /**
     * Retrieves wcc_ident
     *
     * @return  int
     */
    public function getWcc_ident() {
      return $this->wcc_ident;
    }
      
    /**
     * Sets wcc_ident
     *
     * @param   int wcc_ident
     * @return  int the previous value
     */
    public function setWcc_ident($wcc_ident) {
      return $this->_change('wcc_ident', $wcc_ident);
    }

    /**
     * Retrieves message_id
     *
     * @return  int
     */
    public function getMessage_id() {
      return $this->message_id;
    }
      
    /**
     * Sets message_id
     *
     * @param   int message_id
     * @return  int the previous value
     */
    public function setMessage_id($message_id) {
      return $this->_change('message_id', $message_id);
    }

    /**
     * Retrieves to_workflow_id
     *
     * @return  int
     */
    public function getTo_workflow_id() {
      return $this->to_workflow_id;
    }
      
    /**
     * Sets to_workflow_id
     *
     * @param   int to_workflow_id
     * @return  int the previous value
     */
    public function setTo_workflow_id($to_workflow_id) {
      return $this->_change('to_workflow_id', $to_workflow_id);
    }

    /**
     * Retrieves the Message entity
     * referenced by message_id=>message_id
     *
     * @return  de.schlund.db.methadon.Message entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getMessage() {
      $r= XPClass::forName('de.schlund.db.methadon.Message')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('message_id', $this->getMessage_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves the Workflow entity
     * referenced by workflow_id=>to_workflow_id
     *
     * @return  de.schlund.db.methadon.Workflow entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTo_workflow() {
      $r= XPClass::forName('de.schlund.db.methadon.Workflow')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('workflow_id', $this->getTo_workflow_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>