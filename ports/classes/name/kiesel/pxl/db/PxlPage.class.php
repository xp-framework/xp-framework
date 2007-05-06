<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 56677 2007-03-21 14:10:03Z rdoebele $
 */
 
  uses('rdbms.DataSet');
 
  /**
   * Class wrapper for table page, database main
   * (Auto-generated on Sun, 06 May 2007 20:53:06 +0200 by Alex)
   *
   * @purpose  Datasource accessor
   */
  class PxlPage extends DataSet {
    public
      $page_id            = 0,
      $bz_id              = 0,
      $author_id          = 0,
      $title              = '',
      $description        = NULL,
      $permalink          = NULL,
      $sequence           = 0,
      $published          = NULL,
      $lastchange         = NULL,
      $changedby          = NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('page');
        $peer->setConnection('pxl');
        $peer->setIdentity('page_id');
        $peer->setPrimary(array('page_id'));
        $peer->setTypes(array(
          'page_id'             => array('%d', FieldType::INT, FALSE),
          'bz_id'               => array('%d', FieldType::INT, FALSE),
          'author_id'           => array('%d', FieldType::INT, FALSE),
          'title'               => array('%s', FieldType::VARCHAR, FALSE),
          'description'         => array('%s', FieldType::TEXT, TRUE),
          'permalink'           => array('%s', FieldType::VARCHAR, TRUE),
          'sequence'            => array('%d', FieldType::INT, FALSE),
          'published'           => array('%s', FieldType::DATETIME, TRUE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE),
          'changedby'           => array('%s', FieldType::VARCHAR, TRUE)
        ));
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
     * Gets an instance of this object by index "i_page_published"
     * 
     * @param   util.Date published
     * @return  name.kiesel.pxl.db.PxlPage[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPublished($published) {
      return self::getPeer()->doSelect(new Criteria(array('published', $published, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "i_seq"
     * 
     * @param   int sequence
     * @return  name.kiesel.pxl.db.PxlPage entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getBySequence($sequence) {
      $r= self::getPeer()->doSelect(new Criteria(array('sequence', $sequence, EQUAL)));
      return $r ? $r[0] : NULL;
    }

    /**
     * Gets an instance of this object by index "pk_page"
     * 
     * @param   int page_id
     * @return  name.kiesel.pxl.db.PxlPage entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPage_id($page_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('page_id', $page_id, EQUAL)));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves page_id
     *
     * @return  int
     */
    public function getPage_id() {
      return $this->page_id;
    }
      
    /**
     * Sets page_id
     *
     * @param   int page_id
     * @return  int the previous value
     */
    public function setPage_id($page_id) {
      return $this->_change('page_id', $page_id);
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
     * Retrieves author_id
     *
     * @return  int
     */
    public function getAuthor_id() {
      return $this->author_id;
    }
      
    /**
     * Sets author_id
     *
     * @param   int author_id
     * @return  int the previous value
     */
    public function setAuthor_id($author_id) {
      return $this->_change('author_id', $author_id);
    }

    /**
     * Retrieves title
     *
     * @return  string
     */
    public function getTitle() {
      return $this->title;
    }
      
    /**
     * Sets title
     *
     * @param   string title
     * @return  string the previous value
     */
    public function setTitle($title) {
      return $this->_change('title', $title);
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
     * Retrieves permalink
     *
     * @return  string
     */
    public function getPermalink() {
      return $this->permalink;
    }
      
    /**
     * Sets permalink
     *
     * @param   string permalink
     * @return  string the previous value
     */
    public function setPermalink($permalink) {
      return $this->_change('permalink', $permalink);
    }

    /**
     * Retrieves sequence
     *
     * @return  int
     */
    public function getSequence() {
      return $this->sequence;
    }
      
    /**
     * Sets sequence
     *
     * @param   int sequence
     * @return  int the previous value
     */
    public function setSequence($sequence) {
      return $this->_change('sequence', $sequence);
    }

    /**
     * Retrieves published
     *
     * @return  util.Date
     */
    public function getPublished() {
      return $this->published;
    }
      
    /**
     * Sets published
     *
     * @param   util.Date published
     * @return  util.Date the previous value
     */
    public function setPublished($published) {
      return $this->_change('published', $published);
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
  }
?>