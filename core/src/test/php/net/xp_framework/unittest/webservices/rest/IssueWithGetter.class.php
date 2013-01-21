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
  class net·xp_framework·unittest·webservices·rest·IssueWithGetter extends Object {
    protected $issueId= 0;
    protected $title= NULL;
    protected $createdAt= NULL;
    
    /**
     * Constructor
     *
     * @param   int issueId
     * @param   string title
     */
    public function __construct($issueId= 0, $title= NULL, $createdAt= NULL) {
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
?>
