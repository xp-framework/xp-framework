<?php namespace net\xp_framework\unittest\rdbms\dataset;

use rdbms\Statement;
use rdbms\finder\Finder;


/**
 * Finder for Job objects
 *
 * @purpose  Finder implementation
 */
class JobFinder extends Finder {

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
    return new \rdbms\Criteria(array('job_id', $pk, EQUAL));
  }
  
  /**
   * Finds newest jobs
   *
   * @return  rdbms.Criteria
   */
  #[@finder(kind= COLLECTION)]
  public function newestJobs() {
    return create(new \rdbms\Criteria())->addOrderBy('valid_from', DESCENDING);
  }

  /**
   * Finds expired jobs
   *
   * @return  rdbms.Criteria
   */
  #[@finder(kind= COLLECTION)]
  public function expiredJobs() {
    return new \rdbms\Criteria(array('expire_at', \util\Date::now(), GREATER_THAN));
  }

  /**
   * Finds jobs with a title similar to the specified title
   *
   * @param   string title
   * @return  rdbms.Criteria
   */
  #[@finder(kind= COLLECTION)]
  public function similarTo($title) {
    return new Statement('select object(j) from job j where title like %s', $title.'%');
  }
}
