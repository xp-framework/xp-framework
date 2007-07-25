<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('rdbms.DataSet', 'util.HashmapIterator');

  /**
   * Class wrapper for table mmessage, database Ruben_Test_PS
   * (Auto-generated on Tue, 24 Jul 2007 12:23:26 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Mmessage extends DataSet {
    public
      $message_id         = 0,
      $title              = '',
      $body               = '',
      $valid_from         = NULL,
      $expire_at          = NULL,
      $recipient_id       = 0,
      $author_id          = 0;
  
    protected
      $cache= array(
        'Author' => array(),
        'Recipient' => array(),
      );

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('Ruben_Test_PS.mmessage');
        $peer->setConnection('localhost');
        $peer->setIdentity('message_id');
        $peer->setPrimary(array('message_id'));
        $peer->setTypes(array(
          'message_id'          => array('%d', FieldType::INT, FALSE),
          'title'               => array('%s', FieldType::VARCHAR, FALSE),
          'body'                => array('%s', FieldType::TEXT, FALSE),
          'valid_from'          => array('%s', FieldType::DATETIME, TRUE),
          'expire_at'           => array('%s', FieldType::DATETIME, FALSE),
          'recipient_id'        => array('%d', FieldType::INT, FALSE),
          'author_id'           => array('%d', FieldType::INT, FALSE)
        ));
        $peer->setRelations(array(
          'Author' => array(
            'classname' => 'de.schlund.db.rubentest.Mperson',
            'key'       => array(
              'author_id' => 'person_id',
            ),
          ),
          'Recipient' => array(
            'classname' => 'de.schlund.db.rubentest.Mperson',
            'key'       => array(
              'recipient_id' => 'person_id',
            ),
          ),
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
     * column factory
     *
     * @param   string name
     * @return  rdbms.Column
     * @throws  lang.IllegalArgumentException
     */
    public static function column($name) {
      return Peer::forName(__CLASS__)->column($name);
    }
  
    /**
     * Gets an instance of this object by index "PRIMARY"
     * 
     * @param   int message_id
     * @return  de.schlund.db.rubentest.Mmessage entity object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByMessage_id($message_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('message_id', $message_id, EQUAL)));
      return $r ? $r[0] : NULL;
    }

    /**
     * Gets an instance of this object by index "recipient_id"
     * 
     * @param   int recipient_id
     * @return  de.schlund.db.rubentest.Mmessage[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByRecipient_id($recipient_id) {
      return self::getPeer()->doSelect(new Criteria(array('recipient_id', $recipient_id, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "author_id"
     * 
     * @param   int author_id
     * @return  de.schlund.db.rubentest.Mmessage[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByAuthor_id($author_id) {
      return self::getPeer()->doSelect(new Criteria(array('author_id', $author_id, EQUAL)));
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
     * Retrieves body
     *
     * @return  string
     */
    public function getBody() {
      return $this->body;
    }
      
    /**
     * Sets body
     *
     * @param   string body
     * @return  string the previous value
     */
    public function setBody($body) {
      return $this->_change('body', $body);
    }

    /**
     * Retrieves valid_from
     *
     * @return  util.Date
     */
    public function getValid_from() {
      return $this->valid_from;
    }
      
    /**
     * Sets valid_from
     *
     * @param   util.Date valid_from
     * @return  util.Date the previous value
     */
    public function setValid_from($valid_from) {
      return $this->_change('valid_from', $valid_from);
    }

    /**
     * Retrieves expire_at
     *
     * @return  util.Date
     */
    public function getExpire_at() {
      return $this->expire_at;
    }
      
    /**
     * Sets expire_at
     *
     * @param   util.Date expire_at
     * @return  util.Date the previous value
     */
    public function setExpire_at($expire_at) {
      return $this->_change('expire_at', $expire_at);
    }

    /**
     * Retrieves recipient_id
     *
     * @return  int
     */
    public function getRecipient_id() {
      return $this->recipient_id;
    }
      
    /**
     * Sets recipient_id
     *
     * @param   int recipient_id
     * @return  int the previous value
     */
    public function setRecipient_id($recipient_id) {
      return $this->_change('recipient_id', $recipient_id);
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
     * Retrieves the Mperson entity
     * referenced by person_id=>author_id
     *
     * @return  de.schlund.db.rubentest.Mperson entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getAuthor() {
      $r= ($this->cached['Author']) ?
        array_values($this->cache['Author']) :
        XPClass::forName('de.schlund.db.rubentest.Mperson')
          ->getMethod('getPeer')
          ->invoke()
          ->doSelect(new Criteria(
          array('person_id', $this->getAuthor_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves the Mperson entity
     * referenced by person_id=>recipient_id
     *
     * @return  de.schlund.db.rubentest.Mperson entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getRecipient() {
      $r= ($this->cached['Recipient']) ?
        array_values($this->cache['Recipient']) :
        XPClass::forName('de.schlund.db.rubentest.Mperson')
          ->getMethod('getPeer')
          ->invoke()
          ->doSelect(new Criteria(
          array('person_id', $this->getRecipient_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>