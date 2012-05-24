<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_framework.unittest.webservices.rest';

  /**
   * Issues
   *
   */
  class net·xp_framework·unittest·webservices·rest·IssuesWithSetter extends Object {
    protected $issues= NULL;

    /**
     * Constructor
     *
     * @param   net.xp_framework.unittest.webservices.rest.IssueWithField[] issues
     */
    public function __construct($issues= NULL) {
      $this->issues= $issues;
    }

    /**
     * Sets issues
     *
     * @param   net.xp_framework.unittest.webservices.rest.IssueWithField[] issues
     */
    public function setIssues($issues) {
      $this->issues= $issues;
    }

    /**
     * Check whether another object is equal to this
     * 
     * @param   var cmp
     * @return  bool
     */
    public function equals($cmp) {
      if (!($cmp instanceof self)) return FALSE;
      if (sizeof($this->issues) !== sizeof($cmp->issues)) return FALSE;
      foreach ($this->issues as $i => $issue) {
        if (!$issue->equals($cmp->issues[$i])) return FALSE;
      }
      return TRUE;
    }

    /**
     * Creates a string representation
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'@'.xp::stringOf($this->issues);
    }
  }
?>
