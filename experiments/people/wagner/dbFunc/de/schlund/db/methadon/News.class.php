<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table news, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class News extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..news');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array('news_id'));
        $peer->setTypes(array(
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'priority'            => array('%d', FieldType::INT, FALSE),
          'news_id'             => array('%d', FieldType::NUMERIC, FALSE),
          'owner_id'            => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE),
          'ts_valid_start'      => array('%s', FieldType::SMALLDATETIME, FALSE),
          'ts_valid_end'        => array('%s', FieldType::SMALLDATETIME, FALSE)
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
     * Gets an instance of this object by index "news_I1"
     * 
     * @param   util.Date ts_valid_start
     * @return  de.schlund.db.methadon.News[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByTs_valid_start($ts_valid_start) {
      $r= self::getPeer()->doSelect(new Criteria(array('ts_valid_start', $ts_valid_start, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
    }

    /**
     * Gets an instance of this object by index "news_I2"
     * 
     * @param   util.Date ts_valid_end
     * @return  de.schlund.db.methadon.News[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByTs_valid_end($ts_valid_end) {
      $r= self::getPeer()->doSelect(new Criteria(array('ts_valid_end', $ts_valid_end, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
    }

    /**
     * Gets an instance of this object by index "PK_NEWS"
     * 
     * @param   int news_id
     * @return  de.schlund.db.methadon.News entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByNews_id($news_id) {
      return new self(array(
        'news_id'  => $news_id,
        '_loadCrit' => new Criteria(array('news_id', $news_id, EQUAL))
      ));
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
     * Retrieves priority
     *
     * @return  int
     */
    public function getPriority() {
      return $this->priority;
    }
      
    /**
     * Sets priority
     *
     * @param   int priority
     * @return  int the previous value
     */
    public function setPriority($priority) {
      return $this->_change('priority', $priority);
    }

    /**
     * Retrieves news_id
     *
     * @return  int
     */
    public function getNews_id() {
      return $this->news_id;
    }
      
    /**
     * Sets news_id
     *
     * @param   int news_id
     * @return  int the previous value
     */
    public function setNews_id($news_id) {
      return $this->_change('news_id', $news_id);
    }

    /**
     * Retrieves owner_id
     *
     * @return  int
     */
    public function getOwner_id() {
      return $this->owner_id;
    }
      
    /**
     * Sets owner_id
     *
     * @param   int owner_id
     * @return  int the previous value
     */
    public function setOwner_id($owner_id) {
      return $this->_change('owner_id', $owner_id);
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
     * Retrieves ts_valid_start
     *
     * @return  util.Date
     */
    public function getTs_valid_start() {
      return $this->ts_valid_start;
    }
      
    /**
     * Sets ts_valid_start
     *
     * @param   util.Date ts_valid_start
     * @return  util.Date the previous value
     */
    public function setTs_valid_start($ts_valid_start) {
      return $this->_change('ts_valid_start', $ts_valid_start);
    }

    /**
     * Retrieves ts_valid_end
     *
     * @return  util.Date
     */
    public function getTs_valid_end() {
      return $this->ts_valid_end;
    }
      
    /**
     * Sets ts_valid_end
     *
     * @param   util.Date ts_valid_end
     * @return  util.Date the previous value
     */
    public function setTs_valid_end($ts_valid_end) {
      return $this->_change('ts_valid_end', $ts_valid_end);
    }

    /**
     * Retrieves the Person entity
     * referenced by person_id=>owner_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getOwner() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getOwner_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves the Document entity
     * referenced by document_id=>news_id
     *
     * @return  de.schlund.db.methadon.Document entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getNews() {
      $r= XPClass::forName('de.schlund.db.methadon.Document')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('document_id', $this->getNews_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
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
  }
?>