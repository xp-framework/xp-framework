<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table pim_note, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Pim_note extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..pim_note');
        $peer->setConnection('sybintern');
        $peer->setIdentity('note_id');
        $peer->setPrimary(array('note_id'));
        $peer->setTypes(array(
          'text'                => array('%s', FieldType::VARCHAR, FALSE),
          'note_id'             => array('%d', FieldType::NUMERIC, FALSE),
          'person_id'           => array('%d', FieldType::NUMERIC, FALSE)
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
     * Gets an instance of this object by index "PK_NOTE"
     * 
     * @param   int note_id
     * @return  de.schlund.db.methadon.Pim_note entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByNote_id($note_id) {
      return new self(array(
        'note_id'  => $note_id,
        '_loadCrit' => new Criteria(array('note_id', $note_id, EQUAL))
      ));
    }

    /**
     * Retrieves text
     *
     * @return  string
     */
    public function getText() {
      return $this->text;
    }
      
    /**
     * Sets text
     *
     * @param   string text
     * @return  string the previous value
     */
    public function setText($text) {
      return $this->_change('text', $text);
    }

    /**
     * Retrieves note_id
     *
     * @return  int
     */
    public function getNote_id() {
      return $this->note_id;
    }
      
    /**
     * Sets note_id
     *
     * @param   int note_id
     * @return  int the previous value
     */
    public function setNote_id($note_id) {
      return $this->_change('note_id', $note_id);
    }

    /**
     * Retrieves person_id
     *
     * @return  int
     */
    public function getPerson_id() {
      return $this->person_id;
    }
      
    /**
     * Sets person_id
     *
     * @param   int person_id
     * @return  int the previous value
     */
    public function setPerson_id($person_id) {
      return $this->_change('person_id', $person_id);
    }

    /**
     * Retrieves the Person entity
     * referenced by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>