<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.mock.arguments.TypeMatcher',
    'unittest.mock.MockRepository',
    'lang.Type',
    'lang.Object',
    'util.Date'   
  );

  /**
   * Testcase for TypeMatcher class
   *
   * @see   xp://unittest.mock.arguments.TypeMatcher
   */
  class TypeMatcherTest extends TestCase {

    /**
     * Verify TypeMatcher creation
     *
     */
    #[@test]
    public function canCreate() {
      new TypeMatcher('test');
    }
    
    /**
     * Test type matching
     *
     */
    #[@test]
    public function object_matches_object() {
      $this->assertTrue(create(new TypeMatcher('lang.Object'))->matches(new Object()));
    }

    /**
     * Test type matching
     *
     */
    #[@test]
    public function object_does_not_match_string() {
      $this->assertFalse(create(new TypeMatcher('lang.Object'))->matches('a string'));
    }

    /**
     * Test type matching
     *
     */
    #[@test]
    public function object_does_not_match_subtype() {
      $this->assertFalse(create(new TypeMatcher('lang.Object'))->matches(new Date()));
    }

    /**
     * Test type matching
     *
     */
    #[@test]
    public function matches_should_not_match_parenttype() {
      $this->assertFalse(create(new TypeMatcher('unittest.mock.arguments.TypeMatcher'))->matches(new Object()));
    }
    
    /**
     * Test NULL matching
     *
     */
    #[@test]
    public function object_matches_null() {
      $this->assertTrue(create(new TypeMatcher('lang.Object'))->matches(NULL));
    }
    
    /**
     * Test NULL matching
     *
     */
    #[@test]
    public function matches_should_not_match_null_if_defined_so() {
      $this->assertFalse(create(new TypeMatcher('lang.Object', FALSE))->matches(NULL));
    }
    
    /**
     * Test works inside MockRepository
     *
     */
    #[@test]
    public function mock_repository_should_work_with() {
      $mockery= new MockRepository();
      $interface= $mockery->createMock('net.xp_framework.unittest.tests.mock.IComplexInterface');
      $interface->fooWithTypeHint(Arg::anyOfType('net.xp_framework.unittest.tests.mock.IEmptyInterface'));
    }
  }
?>
