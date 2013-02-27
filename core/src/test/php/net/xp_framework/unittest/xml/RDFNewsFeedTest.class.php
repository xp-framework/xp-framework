<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase', 'xml.rdf.RDFNewsFeed');

  /**
   * TestCase
   *
   * @see  xp://xml.rdf.RDFNewsFeed
   */
  class RDFNewsFeedTest extends TestCase {
  
    /**
     * Test
     *
     */
    #[@test]
    public function can_create() {
      new RDFNewsFeed();
    }
  }
?>
