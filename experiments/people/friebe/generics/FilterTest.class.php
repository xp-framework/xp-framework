<?php
/* This class is part of the XP framework's experiments
 *
 * $Id: GenericsTest.class.php 9174 2007-01-08 12:18:47Z friebe $
 */

  uses(
    'unittest.TestCase',
    'text.String',
    'generic+xp://URIContainsFilter',
    'io.collections.FileElement'
  );

  /**
   * Tests generics
   *
   * @purpose  Unit test
   */
  class FilterTest extends TestCase {
  
    /**
     * Tests type hinting
     *
     */
    #[@test]
    public function newFilter() {
      $filter= new URIContainsFilter('home');
      $this->assertTrue($filter->accept(new FileElement('/home/thekid')));
    }
  }
?>

