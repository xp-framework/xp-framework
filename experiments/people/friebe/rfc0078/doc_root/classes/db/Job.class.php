<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('rdbms.DataSet');
 
  /**
   * Class wrapper for table job, database test
   * (Auto-generated on Fri, 12 Jan 2007 17:56:00 +0100 by thekid)
   *
   * @purpose  Datasource accessor
   */
  class Job extends DataSet {
    public
      $job_id             = 0,
      $location           = '',
      $category           = '',
      $title              = '',
      $valid_from         = NULL,
      $expire_at          = NULL,
      $tasks              = NULL,
      $requirements       = NULL,
      $created_at         = NULL,
      $lastchange         = NULL,
      $changedby          = '',
      $bz_id              = 0;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('test.job');
        $peer->setConnection('jobs');
        $peer->setIdentity('job_id');
        $peer->setPrimary(array('job_id'));
        $peer->setTypes(array(
          'job_id'              => array('%d', FieldType::INT, FALSE),
          'location'            => array('%s', FieldType::VARCHAR, FALSE),
          'category'            => array('%s', FieldType::VARCHAR, FALSE),
          'title'               => array('%s', FieldType::VARCHAR, FALSE),
          'valid_from'          => array('%s', FieldType::DATETIME, FALSE),
          'expire_at'           => array('%s', FieldType::DATETIME, FALSE),
          'tasks'               => array('%s', FieldType::TEXT, TRUE),
          'requirements'        => array('%s', FieldType::TEXT, TRUE),
          'created_at'          => array('%s', FieldType::DATETIME, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'bz_id'               => array('%d', FieldType::INT, FALSE)
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
     * Gets an instance of this object by index "PRIMARY"
     * 
     * @param   int job_id
     * @return  classes.db.Job object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByJob_id($job_id) {
      return current(self::getPeer()->doSelect(new Criteria(array('job_id', $job_id, EQUAL))));
    }

    /**
     * Gets an instance of this object by index "validity"
     * 
     * @param   int bz_id
     * @param   util.Date valid_from
     * @param   util.Date expire_at
     * @return  classes.db.Job[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByBz_idValid_fromExpire_at($bz_id, $valid_from, $expire_at) {
      return self::getPeer()->doSelect(new Criteria(
        array('bz_id', $bz_id, EQUAL),
        array('valid_from', $valid_from, EQUAL),
        array('expire_at', $expire_at, EQUAL)
      ));
    }

    /**
     * Gets an instance of this object by index "sorting"
     * 
     * @param   util.Date created_at
     * @return  classes.db.Job[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByCreated_at($created_at) {
      return self::getPeer()->doSelect(new Criteria(array('created_at', $created_at, EQUAL)));
    }

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
     * Retrieves location
     *
     * @return  string
     */
    public function getLocation() {
      return $this->location;
    }
      
    /**
     * Sets location
     *
     * @param   string location
     * @return  string the previous value
     */
    public function setLocation($location) {
      return $this->_change('location', $location);
    }

    /**
     * Retrieves category
     *
     * @return  string
     */
    public function getCategory() {
      return $this->category;
    }
      
    /**
     * Sets category
     *
     * @param   string category
     * @return  string the previous value
     */
    public function setCategory($category) {
      return $this->_change('category', $category);
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
     * Retrieves tasks
     *
     * @return  string
     */
    public function getTasks() {
      return $this->tasks;
    }
      
    /**
     * Sets tasks
     *
     * @param   string tasks
     * @return  string the previous value
     */
    public function setTasks($tasks) {
      return $this->_change('tasks', $tasks);
    }

    /**
     * Retrieves requirements
     *
     * @return  string
     */
    public function getRequirements() {
      return $this->requirements;
    }
      
    /**
     * Sets requirements
     *
     * @param   string requirements
     * @return  string the previous value
     */
    public function setRequirements($requirements) {
      return $this->_change('requirements', $requirements);
    }

    /**
     * Retrieves created_at
     *
     * @return  util.Date
     */
    public function getCreated_at() {
      return $this->created_at;
    }
      
    /**
     * Sets created_at
     *
     * @param   util.Date created_at
     * @return  util.Date the previous value
     */
    public function setCreated_at($created_at) {
      return $this->_change('created_at', $created_at);
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
  }
?>