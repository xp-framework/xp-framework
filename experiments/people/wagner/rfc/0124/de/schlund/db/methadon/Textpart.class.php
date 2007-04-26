<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table textpart, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Textpart extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..textpart');
        $peer->setConnection('sybintern');
        $peer->setIdentity('textpart_id');
        $peer->setPrimary(array('textpart_id'));
        $peer->setTypes(array(
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'description'         => array('%s', FieldType::VARCHAR, TRUE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'textpart_id'         => array('%d', FieldType::NUMERIC, FALSE),
          'language_id'         => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE),
          'continuous_text'     => array('%s', FieldType::TEXT, TRUE)
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
     * Gets an instance of this object by index "U1_textpart"
     * 
     * @param   string name
     * @param   int language_id
     * @return  de.schlund.db.methadon.Textpart entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByNameLanguage_id($name, $language_id) {
      return new self(array(
        'name'  => $name,
        'language_id'  => $language_id,
        '_loadCrit' => new Criteria(
          array('name', $name, EQUAL),
          array('language_id', $language_id, EQUAL)
        )
      ));
    }

    /**
     * Gets an instance of this object by index "PK_TEXTPART"
     * 
     * @param   int textpart_id
     * @return  de.schlund.db.methadon.Textpart entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByTextpart_id($textpart_id) {
      return new self(array(
        'textpart_id'  => $textpart_id,
        '_loadCrit' => new Criteria(array('textpart_id', $textpart_id, EQUAL))
      ));
    }

    /**
     * Retrieves name
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }
      
    /**
     * Sets name
     *
     * @param   string name
     * @return  string the previous value
     */
    public function setName($name) {
      return $this->_change('name', $name);
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
     * Retrieves language_id
     *
     * @return  int
     */
    public function getLanguage_id() {
      return $this->language_id;
    }
      
    /**
     * Sets language_id
     *
     * @param   int language_id
     * @return  int the previous value
     */
    public function setLanguage_id($language_id) {
      return $this->_change('language_id', $language_id);
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
     * Retrieves continuous_text
     *
     * @return  string
     */
    public function getContinuous_text() {
      return $this->continuous_text;
    }
      
    /**
     * Sets continuous_text
     *
     * @param   string continuous_text
     * @return  string the previous value
     */
    public function setContinuous_text($continuous_text) {
      return $this->_change('continuous_text', $continuous_text);
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
     * Retrieves the Language entity
     * referenced by language_id=>language_id
     *
     * @return  de.schlund.db.methadon.Language entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getLanguage() {
      $r= XPClass::forName('de.schlund.db.methadon.Language')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('language_id', $this->getLanguage_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves an array of all Textpart_matrix entities referencing
     * this entity by textpart_id=>textpart_id
     *
     * @return  de.schlund.db.methadon.Textpart_matrix[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTextpart_matrixTextpartList() {
      return XPClass::forName('de.schlund.db.methadon.Textpart_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('textpart_id', $this->getTextpart_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Textpart_matrix entities referencing
     * this entity by textpart_id=>textpart_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Textpart_matrix>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTextpart_matrixTextpartIterator() {
      return XPClass::forName('de.schlund.db.methadon.Textpart_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('textpart_id', $this->getTextpart_id(), EQUAL)
      ));
    }
  }
?>