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
   * A proxy derivitive which implements additional mock behaviour definition
   * and validation.
   *
   * @see      xp://unittest.mock.MockProxy
   * @purpose  Unit Test
   */
  class TypeMatcherTest extends TestCase {
     /**
     * Can create.
     */
    #[@test]
    public function canCreate() {
      new TypeMatcher("test");
    }
    
    /**
     * Can create.
     */
    #[@test]
    public function matches_should_match_Type() {
      $sut= new TypeMatcher("lang.Object");
      $this->assertTrue($sut->matches(new Object()));
      $this->assertFalse($sut->matches("a string"));
      $this->assertFalse($sut->matches(new Date()));

      
      $sut= new TypeMatcher("unittest.mock.arguments.TypeMatcher");
      $this->assertTrue($sut->matches(new TypeMatcher("foo")));
      $this->assertFalse($sut->matches(new Object()));
      $this->assertFalse($sut->matches(new Date()));
    }
    
    /**
     * Can create.
     */
    #[@test]
    public function matches_should_match_null() {
      $sut= new TypeMatcher("lang.Object");
      $this->assertTrue($sut->matches(NULL));
      
      $sut= new TypeMatcher("unittest.mock.arguments.TypeMatcher");
      $this->assertTrue($sut->matches(NULL));
    }
    
    /**
     * Can create.
     */
    #[@test]
    public function matches_should_not_match_null_if_defined_so() {
      $sut= new TypeMatcher("lang.Object", FALSE);
      $this->assertFalse($sut->matches(NULL));
      
      $sut= new TypeMatcher("unittest.mock.arguments.TypeMatcher", FALSE);
      $this->assertFalse($sut->matches(NULL));
    }
    
    /**
     * Can create.
     */
    #[@test]
    public function mockery_should_work_with() {
      $mockery= new MockRepository();
      $interface= $mockery->createMock('net.xp_framework.unittest.tests.mock.IComplexInterface');
      $interface->fooWithTypeHint(Arg::anyOfType('net.xp_framework.unittest.tests.mock.IEmptyInterface'));
    }
  }
?>
