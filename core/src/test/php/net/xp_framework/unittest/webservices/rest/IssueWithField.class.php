<?php
/* This class is part of the XP framework
 *
 * $issueId$ 
 */

  $package= 'net.xp_framework.unittest.webservices.rest';

  /**
   * Issue
   *
   */
  class net·xp_framework·unittest·webservices·rest·IssueWithField extends Object {
    public $issueId= 0;
    public $title= NULL;
    
    /**
     * Constructor
     *
     * @param   int issueId
     * @param   string title
     */
    public function __construct($issueId= 0, $title= NULL) {
      $this->issueId= $issueId;
      $this->title= $title;
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
?>
