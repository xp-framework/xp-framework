<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table intranet_notify_todo, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Intranet_notify_todo extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..intranet_notify_todo');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array('message_id'));
        $peer->setTypes(array(
          'message_id'          => array('%d', FieldType::NUMERIC, FALSE)
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
     * Gets an instance of this object by index "PK_INT"
     * 
     * @param   int message_id
     * @return  de.schlund.db.methadon.Intranet_notify_todo entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByMessage_id($message_id) {
      return new self(array(
        'message_id'  => $message_id,
        '_loadCrit' => new Criteria(array('message_id', $message_id, EQUAL))
      ));
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
  }
?>