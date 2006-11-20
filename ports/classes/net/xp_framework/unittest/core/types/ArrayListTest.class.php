<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase', 'lang.types.ArrayList');

  /**
   * Tests the ArrayList class
   *
   * @see      xp://lang.types.ArrayList
   * @purpose  Testcase
   */
  class ArrayListTest extends TestCase {
    var
      $list = NULL;

    /**
     * Setup method. .
     *
     * @access  public
     */
    function setUp() {
      $this->list= &new ArrayList();
    }

    /**
     * Ensures a newly created ArrayList is empty
     *
     * @access  public
     */
    #[@test]
    function initiallyEmpty() {
      $this->assertEquals(0, sizeof($this->list->values));
    }

    /**
     * Ensures a newly created ArrayList is equal to another newly 
     * created ArrayList
     *
     * @access  public
     */
    #[@test]
    function newListsAreEqual() {
      $this->assertEquals($this->list, new ArrayList());
    }
  }
?>
