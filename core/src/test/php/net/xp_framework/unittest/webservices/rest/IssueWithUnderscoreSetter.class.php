<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_framework.unittest.webservices.rest';

  /**
   * Issue
   *
   */
  class net·xp_framework·unittest·webservices·rest·IssueWithUnderscoreSetter extends Object {
    protected $issue_id= 0;
    protected $title= NULL;
    
    /**
     * Constructor
     *
     * @param   int issue_id
     * @param   string title
     */
    public function __construct($issue_id= 0, $title= NULL) {
      $this->issue_id= $issue_id;
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
     * Set issue_id
     *
     * @param   int issue_id
     */
    public function setIssue_id($issue_id) {
      $this->issue_id= $issue_id;
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
?>
