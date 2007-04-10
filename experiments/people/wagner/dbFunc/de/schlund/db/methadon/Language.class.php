<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table language, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Language extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..language');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array('language_id'));
        $peer->setTypes(array(
          'mnemonic'            => array('%s', FieldType::VARCHAR, FALSE),
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'description'         => array('%s', FieldType::VARCHAR, TRUE),
          'language_id'         => array('%d', FieldType::NUMERIC, FALSE)
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
     * Gets an instance of this object by index "U1_language"
     * 
     * @param   string mnemonic
     * @return  de.schlund.db.methadon.Language entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByMnemonic($mnemonic) {
      return new self(array(
        'mnemonic'  => $mnemonic,
        '_loadCrit' => new Criteria(array('mnemonic', $mnemonic, EQUAL))
      ));
    }

    /**
     * Gets an instance of this object by index "PK_LANGUAGE"
     * 
     * @param   int language_id
     * @return  de.schlund.db.methadon.Language entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByLanguage_id($language_id) {
      return new self(array(
        'language_id'  => $language_id,
        '_loadCrit' => new Criteria(array('language_id', $language_id, EQUAL))
      ));
    }

    /**
     * Retrieves mnemonic
     *
     * @return  string
     */
    public function getMnemonic() {
      return $this->mnemonic;
    }
      
    /**
     * Sets mnemonic
     *
     * @param   string mnemonic
     * @return  string the previous value
     */
    public function setMnemonic($mnemonic) {
      return $this->_change('mnemonic', $mnemonic);
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
     * Retrieves an array of all Textpart entities referencing
     * this entity by language_id=>language_id
     *
     * @return  de.schlund.db.methadon.Textpart[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTextpartLanguageList() {
      return XPClass::forName('de.schlund.db.methadon.Textpart')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('language_id', $this->getLanguage_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Textpart entities referencing
     * this entity by language_id=>language_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Textpart>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTextpartLanguageIterator() {
      return XPClass::forName('de.schlund.db.methadon.Textpart')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('language_id', $this->getLanguage_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Document entities referencing
     * this entity by language_id=>language_id
     *
     * @return  de.schlund.db.methadon.Document[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getDocumentLanguageList() {
      return XPClass::forName('de.schlund.db.methadon.Document')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('language_id', $this->getLanguage_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Document entities referencing
     * this entity by language_id=>language_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Document>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getDocumentLanguageIterator() {
      return XPClass::forName('de.schlund.db.methadon.Document')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('language_id', $this->getLanguage_id(), EQUAL)
      ));
    }
  }
?>