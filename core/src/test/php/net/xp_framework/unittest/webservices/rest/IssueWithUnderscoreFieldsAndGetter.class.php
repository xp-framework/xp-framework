<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_framework.unittest.webservices.rest';
  
  uses('util.Date');

  /**
   * Issue
   *
   */
  class net·xp_framework·unittest·webservices·rest·IssueWithUnderscoreFieldsAndGetter extends Object {
    protected $issue_id= 0;
    protected $title= NULL;
    protected $created_at= NULL;
    
    /**
     * Constructor
     *
     * @param   int issue_id
     * @param   string title
     * @param   util.Date created_at
     */
    public function __construct($issue_id= 0, $title= NULL, $created_at= NULL) {
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
?>
