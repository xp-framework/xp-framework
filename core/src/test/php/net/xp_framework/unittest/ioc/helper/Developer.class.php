<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * @purpose  Helper class for test cases.
   */
  #[@providedBy('net.xp_framework.unittest.ioc.helper.DeveloperProvider')]
  interface Developer {
    /**
     * does some coding
     */
    public function code();
  }
?>
