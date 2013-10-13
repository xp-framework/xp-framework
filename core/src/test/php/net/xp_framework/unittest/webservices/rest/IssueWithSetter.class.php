<?php namespace net\xp_framework\unittest\webservices\rest;



/**
 * Issue
 *
 */
class IssueWithSetter extends \lang\Object {
  protected $issueId= 0;
  protected $title= null;
  
  /**
   * Constructor
   *
   * @param   int issueId
   * @param   string title
   */
  public function __construct($issueId= 0, $title= null) {
    $this->issueId= $issueId;
    $this->title= $title;
  }

  /**
   * Set title
   *
   * @param   string title
   */
  public function setTitle($title) {
    $this->title= $title;
  }

  /**
   * Set issueId
   *
   * @param   int issueId
   */
  public function setIssueId($issueId) {
    $this->issueId= $issueId;
  }
  
  /**
   * Checks whether another object is equal to this issue
   *
   * @param   var cmp
   * @return  bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && $cmp->issueId === $this->issueId && $cmp->title === $this->title;
  }
}
