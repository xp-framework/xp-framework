<?php
/* This class is part of the XP framework
 *
 * $Id: Job.class.php 9512 2007-02-27 17:36:28Z friebe $
 */
 
  uses(
    'rdbms.join.JoinExtractable',
    'rdbms.DataSet'
  );
 
  /**
   * Class wrapper for table job, database JOBS
   *
   * @purpose  Datasource accessor
   */
  class Job extends DataSet implements JoinExtractable {
    public
      $job_id             = 0,
      $title              = '',
      $valid_from         = NULL,
      $expire_at          = NULL;

    private
      $cache              = array(),
      $cached             = array();

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('JOBS.job');
        $peer->setConnection('jobs');
        $peer->setIdentity('job_id');
        $peer->setPrimary(array('job_id'));
        $peer->setTypes(array(
          'job_id'      => array('%d', FieldType::NUMERIC, FALSE),
          'title'       => array('%s', FieldType::VARCHAR, FALSE),
          'valid_from'  => array('%s', FieldType::VARCHAR, TRUE),
          'expire_at'   => array('%s', FieldType::DATETIME, FALSE),
        ));
        $peer->setConstraints(array(
          'JobPerson' => array(
            'classname' => 'net.xp_framework.unittest.rdbms.dataset.Person',
            'key'       => array(
              'job_id' => 'job_id',
            ),
          ),
        ));
      }
    }  
  
    public function setCachedObj($role, $key, $obj) { $this->chache[$role][$key]= $obj; }
    public function getCachedObj($role, $key) { return $this->chache[$role][$key]; }
    public function hasCachedObj($role, $key) { return isset($this->chache[$role][$key]); }
    public function markAsCached($role) { $this->cached[$role]= TRUE; }

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
     * @return  net.xp_framework.unittest.rdbms.dataset.Job object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByJob_id($job_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('job_id', $job_id, EQUAL)));
      return $r ? $r[0] : NULL;
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
  }
?>
