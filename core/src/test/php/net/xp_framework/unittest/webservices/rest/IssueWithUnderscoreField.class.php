<?php namespace net\xp_framework\unittest\webservices\rest;



/**
 * Issue
 *
 */
class IssueWithUnderscoreField extends \lang\Object {
  public $issue_id= 0;
  public $title= null;
  
  /**
   * Constructor
   *
   * @param   int issue_id
   * @param   string title
   */
  public function __construct($issue_id= 0, $title= null) {
    $this->issue_id= $issue_id;
    $this->title= $title;
  }

  /**
   * Checks whether another object is equal to this issue
   *
   * @param   var cmp
   * @return  bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && $cmp->issue_id === $this->issue_id && $cmp->title === $this->title;
  }
}
