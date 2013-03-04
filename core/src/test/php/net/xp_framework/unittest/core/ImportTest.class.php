<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  namespace net\xp_framework\unittest\core;

  use \lang\types\ArrayList;
  new \import('net.xp_framework.unittest.core.extensions.ArrayListExtensions');

  /**
   * Tests the XP Framework's import() functionality
   *
   */
  class ImportTest extends \unittest\TestCase {

    /**
     * Test sorted() extension method
     *
     */
    #[@test]
    public function sorted_method() {
      $this->assertEquals(
        new ArrayList(-1, 0, 1, 7, 10),
        create(new ArrayList(7, 0, 10, 1, -1))->sorted(SORT_NUMERIC)
      );
    }
  }
?>
