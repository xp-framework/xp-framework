<?php namespace net\xp_framework\unittest\webservices\rest;



use util\Date;


/**
 * Issue
 *
 */
class IssueWithGetter extends \lang\Object {
  protected $issueId= 0;
  protected $title= null;
  protected $createdAt= null;
  
  /**
   * Constructor
   *
   * @param   int issueId
   * @param   string title
   */
  public function __construct($issueId= 0, $title= null, $createdAt= null) {
    $this->issueId= $issueId;
    $this->title= $title;
    $this->createdAt= $createdAt;
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
    return $this->issueId;
  }

  /**
   * Get created at
   *
   * @return  util.Date createdAt
   */
  public function getCreatedAt() {
    return $this->createdAt;
  }
}
