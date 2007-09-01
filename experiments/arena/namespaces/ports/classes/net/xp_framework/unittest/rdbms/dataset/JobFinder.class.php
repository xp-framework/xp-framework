<?php
/* This class is part of the XP framework
 *
 * $Id: JobFinder.class.php 9348 2007-01-23 11:47:45Z friebe $ 
 */

  namespace net::xp_framework::unittest::rdbms::dataset;

  ::uses(
    'net.xp_framework.unittest.rdbms.dataset.Job',
    'rdbms.Statement',
    'rdbms.finder.Finder'
  );

  /**
   * Finder for Job objects
   *
   * @purpose  Finder implementation
   */
  class JobFinder extends rdbms::finder::Finder {
  
    /**
     * Returns the Peer object for this finder
     *
     * @return  rdbms.Peer
     */
    public function getPeer() {
      return Job::getPeer();
    }
    
    /**
     * Finds a job by its primary key
     *
     * @param   int pk the job_id
     * @return  rdbms.Criteria
     */
    #[@finder(kind= ENTITY)]
    public function byPrimary($pk) {
      return new rdbms::Criteria(array('job_id', $pk, EQUAL));
    }
    
    /**
     * Finds newest jobs
     *
     * @return  rdbms.Criteria
     */
    #[@finder(kind= COLLECTION)]
    public function newestJobs() {
      return rdbms::Criteria::newInstance()->addOrderBy('valid_from', DESCENDING);
    }

    /**
     * Finds expired jobs
     *
     * @return  rdbms.Criteria
     */
    #[@finder(kind= COLLECTION)]
    public function expiredJobs() {
      return new rdbms::Criteria(array('expire_at', util::Date::now(), GREATER_THAN));
    }

    /**
     * Finds jobs with a title similar to the specified title
     *
     * @param   string title
     * @return  rdbms.Criteria
     */
    #[@finder(kind= COLLECTION)]
    public function similarTo($title) {
      return new rdbms::Statement('select object(j) from job j where title like %s', $title.'%');
    }
  }
?>
