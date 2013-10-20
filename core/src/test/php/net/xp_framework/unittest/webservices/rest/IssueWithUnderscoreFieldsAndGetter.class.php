<?php namespace net\xp_framework\unittest\webservices\rest;



use util\Date;


/**
 * Issue
 *
 */
class IssueWithUnderscoreFieldsAndGetter extends \lang\Object {
  protected $issue_id= 0;
  protected $title= null;
  protected $created_at= null;
  
  /**
   * Constructor
   *
   * @param   int issue_id
   * @param   string title
   * @param   util.Date created_at
   */
  public function __construct($issue_id= 0, $title= null, $created_at= null) {
    $this->issue_id= $issue_id;
    $this->title= $title;
    $this->created_at= $created_at;
  }

  /**
   * Get title
   *
   * @return  string title
   */
  public function getTitle() {
    return $this->title;
  }

  /**
   * Get issueId
   *
   * @return  int issueId
   */
  public function getIssueId() {
    return $this->issue_id;
  }

  /**
   * Get created at
   *
   * @return  util.Date createdAt
   */
  public function getCreatedAt() {
    return $this->created_at;
  }
}
