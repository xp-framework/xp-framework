<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'peer.Header'
  );

  /**
   * TestCase
   *
   * @see      xp://peer.Header
   */
  class HeaderTest extends TestCase {
    
    /**
     * Create new Header
     *
     */
    #[@test]
    public function newHeader() {
      $this->assertEquals('name: value', create(new Header('name', 'value'))->toString());
    }

    /**
     * Test header compare
     *
     */
    #[@test]
    public function compareTwoEqualInstances() {
      $this->assertEquals(new Header('name', 'value'), new Header('name', 'value'));
    }

    /**
     * Test header compare
     *
     */
    #[@test]
    public function compareTwoUnequalValueInstances() {
      $this->assertNotEquals(new Header('name', 'value'), new Header('name', 'value1'));
    }

    /**
     * Test header compare
     *
     */
    #[@test]
    public function compareTwoUnequalNameInstances() {
      $this->assertNotEquals(new Header('name', 'value'), new Header('name1', 'value'));
    }

    /**
     * Test header compare
     *
     */
    #[@test]
    public function compareTwoUnequalInstances() {
      $this->assertNotEquals(new Header('name', 'value'), new Header('name1', 'value1'));
    }
  }
?>
