<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.mock.ExpectationList',
       'unittest.mock.Expectation');
  
/**
 * Test cases for the ExpectationList class
 *
 * @purpose Unit tests
 */
  class ExpectationListTest extends TestCase {

    private $sut=null;
    /**
     * Creates the fixture;
     *
     */
    public function setUp() {
      $this->sut=new ExpectationList();
    }
      
    /**
     * Can create.
     */
    #[@test]
    public function canCreate() {
      new ExpectationList();
    }

    /**
     * getNext should exist.          
     */
    #[@test]
    public function canCallGetNext() {
      $this->sut->getNext(array());
    }
    
    /**
     * getNext should return null after initialization.                  
     */
    #[@test]
    public function getNext_returnNullByDefault() {
      $this->assertNull($this->sut->getNext(array()));
    }
    
    /**
     * add method should exist.            
     */
    #[@test]
    public function canAddExpectation() {
      $this->sut->add(new Expectation());
    }
    
    /**
     * Added expectations should be returned by getNext.
     */
    #[@test]
    public function getNextReturnsAddedExpectation() {
      $expect=new Expectation();
      $this->sut->add($expect);
      
      $this->assertEquals($expect, $this->sut->getNext(array()));
    }
    
    /**
     * If no more expectations left, getNext should return null.
     */
    #[@test]
    public function getNextReturnsNullIfNoMoreElements() {
      $expect=new Expectation();
      $this->sut->add($expect);
      
      $this->assertEquals($expect, $this->sut->getNext(array()));
      $this->assertNull($this->sut->getNext(array()));
    }
    
    /**
     * Null shall never be added.
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function cannotAddNull() {
      $this->sut->add(null);
    }
    
    /**
     * Another object shall never be added.
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function cannotAddObjects() {
      $this->sut->add(new Object());
    }


    /**
     * getNext returns an expectation is returned twice if repeat is set to 1
     */
    #[@test]
    public function getNext_SameExpectationTwice_whenRepeatIs2() {
      $expect=new Expectation();
      $expect->setRepeat(2);
      $this->sut->add($expect);

      $this->assertEquals($expect, $this->sut->getNext(array()));
      $this->assertEquals($expect, $this->sut->getNext(array()));
      $this->assertNull($this->sut->getNext(array()));
    }

    /**
     * ExpectationList should provide access to the left over expectations
     */
    #[@test]
    public function should_provide_access_to_left_expectations() {
      $expect=new Expectation();
      $this->sut->add($expect);

      $list= $this->sut->getExpectations();
      $this->assertEquals(1, sizeof($list));
      $this->assertEquals($expect, $list[0]);
    }
    /**
     * ExpectationList should provide access to the "used" expectations
     */
    #[@test]
    public function should_provide_access_to_used_expectations() {
      $expect=new Expectation();
      $this->sut->add($expect);
      $this->sut->getNext(array());
      
      $list= $this->sut->getCalled();
      $this->assertEquals(1, sizeof($list));
      $this->assertEquals($expect, $list[0]);
    }

    /**

     */
  #[@test]
    public function expectation_should_be_moved_to_calledList_after_usage() {
      $expect=new Expectation();
      $this->sut->add($expect);
      $list= $this->sut->getExpectations();
      $this->assertEquals(1, sizeof($list));
      $this->assertEquals($expect, $list[0]);
      
      $this->sut->getNext(array());

      $list= $this->sut->getCalled();
      $this->assertEquals(1, sizeof($list));
      $this->assertEquals($expect, $list[0]);
    }
  }

?>