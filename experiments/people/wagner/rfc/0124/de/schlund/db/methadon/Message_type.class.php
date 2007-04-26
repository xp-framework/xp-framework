<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table message_type, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Message_type extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..message_type');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array('message_type_id'));
        $peer->setTypes(array(
          'description'         => array('%s', FieldType::VARCHAR, TRUE),
          'message_type_id'     => array('%d', FieldType::NUMERIC, FALSE)
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
     * Gets an instance of this object by index "PK_MESSAGE_TYPE"
     * 
     * @param   int message_type_id
     * @return  de.schlund.db.methadon.Message_type entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByMessage_type_id($message_type_id) {
      return new self(array(
        'message_type_id'  => $message_type_id,
        '_loadCrit' => new Criteria(array('message_type_id', $message_type_id, EQUAL))
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
  }
?>