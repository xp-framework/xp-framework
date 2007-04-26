<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table document, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Document extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..document');
        $peer->setConnection('sybintern');
        $peer->setIdentity('document_id');
        $peer->setPrimary(array('document_id'));
        $peer->setTypes(array(
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'description'         => array('%s', FieldType::VARCHAR, TRUE),
          'content_type'        => array('%s', FieldType::VARCHAR, FALSE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'document_id'         => array('%d', FieldType::NUMERIC, FALSE),
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
     * Gets an instance of this object by index "U1_document"
     * 
     * @param   string name
     * @param   int language_id
     * @param   int bz_id
     * @return  de.schlund.db.methadon.Document entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByNameLanguage_idBz_id($name, $language_id, $bz_id) {
      return new self(array(
        'name'  => $name,
        'language_id'  => $language_id,
        'bz_id'  => $bz_id,
        '_loadCrit' => new Criteria(
          array('name', $name, EQUAL),
          array('language_id', $language_id, EQUAL),
          array('bz_id', $bz_id, EQUAL)
        )
      ));
    }

    /**
     * Gets an instance of this object by index "PK_DOCUMENT"
     * 
     * @param   int document_id
     * @return  de.schlund.db.methadon.Document entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByDocument_id($document_id) {
      return new self(array(
        'document_id'  => $document_id,
        '_loadCrit' => new Criteria(array('document_id', $document_id, EQUAL))
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
     * Retrieves content_type
     *
     * @return  string
     */
    public function getContent_type() {
      return $this->content_type;
    }
      
    /**
     * Sets content_type
     *
     * @param   string content_type
     * @return  string the previous value
     */
    public function setContent_type($content_type) {
      return $this->_change('content_type', $content_type);
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
     * Retrieves document_id
     *
     * @return  int
     */
    public function getDocument_id() {
      return $this->document_id;
    }
      
    /**
     * Sets document_id
     *
     * @param   int document_id
     * @return  int the previous value
     */
    public function setDocument_id($document_id) {
      return $this->_change('document_id', $document_id);
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
     * Retrieves an array of all News entities referencing
     * this entity by news_id=>document_id
     *
     * @return  de.schlund.db.methadon.News[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getNewsList() {
      return XPClass::forName('de.schlund.db.methadon.News')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('news_id', $this->getDocument_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all News entities referencing
     * this entity by news_id=>document_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.News>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getNewsIterator() {
      return XPClass::forName('de.schlund.db.methadon.News')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('news_id', $this->getDocument_id(), EQUAL)
      ));
    }
  }
?>