<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table history_message, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class History_message extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..history_message');
        $peer->setConnection('sybintern');
        $peer->setIdentity('history_message_id');
        $peer->setPrimary(array('history_message_id'));
        $peer->setTypes(array(
          'message_subject'     => array('%s', FieldType::VARCHAR, TRUE),
          'message_text'        => array('%s', FieldType::VARCHAR, TRUE),
          'message_return_parameter' => array('%s', FieldType::VARCHAR, TRUE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'changedby2'          => array('%s', FieldType::VARCHAR, FALSE),
          'history_message_id'  => array('%d', FieldType::NUMERIC, FALSE),
          'message_id'          => array('%d', FieldType::NUMERIC, FALSE),
          'message_type_id'     => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'creator_id'          => array('%d', FieldType::NUMERIC, FALSE),
          'creator_right'       => array('%d', FieldType::NUMERIC, FALSE),
          'receiver_id'         => array('%d', FieldType::NUMERIC, FALSE),
          'workflow_id'         => array('%d', FieldType::NUMERIC, FALSE),
          'create_time'         => array('%s', FieldType::DATETIME, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE),
          'workflow_state'      => array('%d', FieldType::INTN, TRUE),
          'message_return_code' => array('%d', FieldType::INTN, TRUE),
          'messagefeature'      => array('%d', FieldType::INTN, TRUE),
          'message_due_date'    => array('%s', FieldType::DATETIMN, TRUE),
          'message_document'    => array('%s', FieldType::TEXT, TRUE),
          'workflow_start_at'   => array('%s', FieldType::SMALLDATETIME, FALSE),
          'reference'           => array('%d', FieldType::NUMERICN, TRUE),
          'message_document_type_id' => array('%d', FieldType::NUMERICN, TRUE)
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
     * Gets an instance of this object by index "PK_HISTORY_MESSAGE"
     * 
     * @param   int history_message_id
     * @return  de.schlund.db.methadon.History_message entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByHistory_message_id($history_message_id) {
      return new self(array(
        'history_message_id'  => $history_message_id,
        '_loadCrit' => new Criteria(array('history_message_id', $history_message_id, EQUAL))
      ));
    }

    /**
     * Retrieves message_subject
     *
     * @return  string
     */
    public function getMessage_subject() {
      return $this->message_subject;
    }
      
    /**
     * Sets message_subject
     *
     * @param   string message_subject
     * @return  string the previous value
     */
    public function setMessage_subject($message_subject) {
      return $this->_change('message_subject', $message_subject);
    }

    /**
     * Retrieves message_text
     *
     * @return  string
     */
    public function getMessage_text() {
      return $this->message_text;
    }
      
    /**
     * Sets message_text
     *
     * @param   string message_text
     * @return  string the previous value
     */
    public function setMessage_text($message_text) {
      return $this->_change('message_text', $message_text);
    }

    /**
     * Retrieves message_return_parameter
     *
     * @return  string
     */
    public function getMessage_return_parameter() {
      return $this->message_return_parameter;
    }
      
    /**
     * Sets message_return_parameter
     *
     * @param   string message_return_parameter
     * @return  string the previous value
     */
    public function setMessage_return_parameter($message_return_parameter) {
      return $this->_change('message_return_parameter', $message_return_parameter);
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
     * Retrieves changedby2
     *
     * @return  string
     */
    public function getChangedby2() {
      return $this->changedby2;
    }
      
    /**
     * Sets changedby2
     *
     * @param   string changedby2
     * @return  string the previous value
     */
    public function setChangedby2($changedby2) {
      return $this->_change('changedby2', $changedby2);
    }

    /**
     * Retrieves history_message_id
     *
     * @return  int
     */
    public function getHistory_message_id() {
      return $this->history_message_id;
    }
      
    /**
     * Sets history_message_id
     *
     * @param   int history_message_id
     * @return  int the previous value
     */
    public function setHistory_message_id($history_message_id) {
      return $this->_change('history_message_id', $history_message_id);
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
     * Retrieves message_type_id
     *
     * @return  int
     */
    public function getMessage_type_id() {
      return $this->message_type_id;
    }
      
    /**
     * Sets message_type_id
     *
     * @param   int message_type_id
     * @return  int the previous value
     */
    public function setMessage_type_id($message_type_id) {
      return $this->_change('message_type_id', $message_type_id);
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
     * Retrieves creator_id
     *
     * @return  int
     */
    public function getCreator_id() {
      return $this->creator_id;
    }
      
    /**
     * Sets creator_id
     *
     * @param   int creator_id
     * @return  int the previous value
     */
    public function setCreator_id($creator_id) {
      return $this->_change('creator_id', $creator_id);
    }

    /**
     * Retrieves creator_right
     *
     * @return  int
     */
    public function getCreator_right() {
      return $this->creator_right;
    }
      
    /**
     * Sets creator_right
     *
     * @param   int creator_right
     * @return  int the previous value
     */
    public function setCreator_right($creator_right) {
      return $this->_change('creator_right', $creator_right);
    }

    /**
     * Retrieves receiver_id
     *
     * @return  int
     */
    public function getReceiver_id() {
      return $this->receiver_id;
    }
      
    /**
     * Sets receiver_id
     *
     * @param   int receiver_id
     * @return  int the previous value
     */
    public function setReceiver_id($receiver_id) {
      return $this->_change('receiver_id', $receiver_id);
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
     * Retrieves create_time
     *
     * @return  util.Date
     */
    public function getCreate_time() {
      return $this->create_time;
    }
      
    /**
     * Sets create_time
     *
     * @param   util.Date create_time
     * @return  util.Date the previous value
     */
    public function setCreate_time($create_time) {
      return $this->_change('create_time', $create_time);
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
     * Retrieves workflow_state
     *
     * @return  int
     */
    public function getWorkflow_state() {
      return $this->workflow_state;
    }
      
    /**
     * Sets workflow_state
     *
     * @param   int workflow_state
     * @return  int the previous value
     */
    public function setWorkflow_state($workflow_state) {
      return $this->_change('workflow_state', $workflow_state);
    }

    /**
     * Retrieves message_return_code
     *
     * @return  int
     */
    public function getMessage_return_code() {
      return $this->message_return_code;
    }
      
    /**
     * Sets message_return_code
     *
     * @param   int message_return_code
     * @return  int the previous value
     */
    public function setMessage_return_code($message_return_code) {
      return $this->_change('message_return_code', $message_return_code);
    }

    /**
     * Retrieves messagefeature
     *
     * @return  int
     */
    public function getMessagefeature() {
      return $this->messagefeature;
    }
      
    /**
     * Sets messagefeature
     *
     * @param   int messagefeature
     * @return  int the previous value
     */
    public function setMessagefeature($messagefeature) {
      return $this->_change('messagefeature', $messagefeature);
    }

    /**
     * Retrieves message_due_date
     *
     * @return  util.Date
     */
    public function getMessage_due_date() {
      return $this->message_due_date;
    }
      
    /**
     * Sets message_due_date
     *
     * @param   util.Date message_due_date
     * @return  util.Date the previous value
     */
    public function setMessage_due_date($message_due_date) {
      return $this->_change('message_due_date', $message_due_date);
    }

    /**
     * Retrieves message_document
     *
     * @return  string
     */
    public function getMessage_document() {
      return $this->message_document;
    }
      
    /**
     * Sets message_document
     *
     * @param   string message_document
     * @return  string the previous value
     */
    public function setMessage_document($message_document) {
      return $this->_change('message_document', $message_document);
    }

    /**
     * Retrieves workflow_start_at
     *
     * @return  util.Date
     */
    public function getWorkflow_start_at() {
      return $this->workflow_start_at;
    }
      
    /**
     * Sets workflow_start_at
     *
     * @param   util.Date workflow_start_at
     * @return  util.Date the previous value
     */
    public function setWorkflow_start_at($workflow_start_at) {
      return $this->_change('workflow_start_at', $workflow_start_at);
    }

    /**
     * Retrieves reference
     *
     * @return  int
     */
    public function getReference() {
      return $this->reference;
    }
      
    /**
     * Sets reference
     *
     * @param   int reference
     * @return  int the previous value
     */
    public function setReference($reference) {
      return $this->_change('reference', $reference);
    }

    /**
     * Retrieves message_document_type_id
     *
     * @return  int
     */
    public function getMessage_document_type_id() {
      return $this->message_document_type_id;
    }
      
    /**
     * Sets message_document_type_id
     *
     * @param   int message_document_type_id
     * @return  int the previous value
     */
    public function setMessage_document_type_id($message_document_type_id) {
      return $this->_change('message_document_type_id', $message_document_type_id);
    }
  }
?>