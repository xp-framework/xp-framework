<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet', 'rdbms.join.JoinExtractable', 'util.HashmapIterator');

  /**
   * Class wrapper for table job, database JOBS
   * (Auto-generated on Wed, 16 May 2007 14:44:35 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Job extends DataSet implements JoinExtractable {
    public
      $job_id             = 0,
      $title              = '',
      $valid_from         = NULL,
      $expire_at          = NULL;
  
    protected
      $cache= array(
        'PersonJob' => array(),
      );

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('JOBS.job');
        $peer->setConnection('jobs');
        $peer->setIdentity('job_id');
        $peer->setPrimary(array('job_id'));
        $peer->setTypes(array(
          'job_id'      => array('%d', FieldType::NUMERIC, FALSE),
          'title'       => array('%s', FieldType::VARCHAR, FALSE),
          'valid_from'  => array('%s', FieldType::DATETIME, TRUE),
          'expire_at'   => array('%s', FieldType::DATETIME, FALSE),
        ));
        $peer->setRelations(array(
          'PersonJob' => array(
            'classname' => 'net.xp_framework.unittest.rdbms.dataset.Person',
            'key'       => array(
              'job_id' => 'job_id',
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
     * @throws  lang.IllegalArumentException
     */
    static public function column($name) {
      return self::getPeer()->column($name);
    }
  
    /**
     * Gets an instance of this object by index "PRIMARY"
     * 
     * @param   int job_id
     * @return  net.xp_framework.unittest.rdbms.dataset.Job entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByJob_id($job_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('job_id', $job_id, EQUAL)));
      return $r ? $r[0] : NULL;    }

    /**
     * Retrieves job_id
     *
     * @return  int
     */
    public function getJob_id() {
      return $this->job_id;
    }
      
    /**
     * Sets job_id
     *
     * @param   int job_id
     * @return  int the previous value
     */
    public function setJob_id($job_id) {
      return $this->_change('job_id', $job_id);
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
     * Retrieves an array of all Person entities referencing
     * this entity by job_id=>job_id
     *
     * @return  net.xp_framework.unittest.rdbms.dataset.Person[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPersonJobList() {
      if ($this->cached['PersonJob']) return array_values($this->cache['PersonJob']);
      return XPClass::forName('net.xp_framework.unittest.rdbms.dataset.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('job_id', $this->getJob_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Person entities referencing
     * this entity by job_id=>job_id
     *
     * @return  rdbms.ResultIterator<net.xp_framework.unittest.rdbms.dataset.Person>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPersonJobIterator() {
      if ($this->cached['PersonJob']) return new HashmapIterator($this->cache['PersonJob']);
      return XPClass::forName('net.xp_framework.unittest.rdbms.dataset.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('job_id', $this->getJob_id(), EQUAL)
      ));
    }
  }
?>
