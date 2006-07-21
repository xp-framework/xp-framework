<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('rdbms.DataSet');
 
  /**
   * Class wrapper for table job, database JOBS
   *
   * @purpose  Datasource accessor
   */
  class Job extends DataSet {
    public
      $job_id             = 0,
      $title              = '',
      $valid_from         = NULL,
      $expire_at          = NULL;

    /**
     * Static initializer
     *
     * @model   static
     * @access  public
     */
    public function __static() { 
      with ($peer= &Job::getPeer()); {
        $peer->setTable('JOBS.job');
        $peer->setConnection('jobs');
        $peer->setIdentity('job_id');
        $peer->setPrimary(array('job_id'));
        $peer->setTypes(array(
          'job_id'              => '%d',
          'title'               => '%s',
          'valid_from'          => '%s',
          'expire_at'           => '%s'
        ));
      }
    }  
  
    /**
     * Retrieve associated peer
     *
     * @access  public
     * @return  &rdbms.Peer
     */
    public function &getPeer() {
      return Peer::forName(__CLASS__);
    }
  
    /**
     * Gets an instance of this object by index "PRIMARY"
     *
     * @access  static
     * @param   int job_id
     * @return  &de.schlund.db.job.Job object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function &getByJob_id($job_id) {
      $peer= &Job::getPeer();
      return array_shift($peer->doSelect(new Criteria(array('job_id', $job_id, EQUAL))));
    }

    /**
     * Retrieves job_id
     *
     * @access  public
     * @return  int
     */
    public function getJob_id() {
      return $this->job_id;
    }
      
    /**
     * Sets job_id
     *
     * @access  public
     * @param   int job_id
     * @return  int the previous value
     */
    public function setJob_id($job_id) {
      return $this->_change('job_id', $job_id);
    }

    /**
     * Retrieves title
     *
     * @access  public
     * @return  string
     */
    public function getTitle() {
      return $this->title;
    }
      
    /**
     * Sets title
     *
     * @access  public
     * @param   string title
     * @return  string the previous value
     */
    public function setTitle($title) {
      return $this->_change('title', $title);
    }

    /**
     * Retrieves valid_from
     *
     * @access  public
     * @return  util.Date
     */
    public function getValid_from() {
      return $this->valid_from;
    }
      
    /**
     * Sets valid_from
     *
     * @access  public
     * @param   util.Date valid_from
     * @return  util.Date the previous value
     */
    public function setValid_from($valid_from) {
      return $this->_change('valid_from', $valid_from);
    }

    /**
     * Retrieves expire_at
     *
     * @access  public
     * @return  util.Date
     */
    public function getExpire_at() {
      return $this->expire_at;
    }
      
    /**
     * Sets expire_at
     *
     * @access  public
     * @param   util.Date expire_at
     * @return  util.Date the previous value
     */
    public function setExpire_at($expire_at) {
      return $this->_change('expire_at', $expire_at);
    }
  }
?>
