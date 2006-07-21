<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.unittest.TestCase',
    'remote.UnknownRemoteObject',
    'util.Binford'
  );

  /**
   * Unit test for UnknownRemoteObject class
   *
   * @see      xp://remote.UnknownRemoteObject
   * @purpose  TestCase
   */
  class UnknownRemoteObjectTest extends TestCase {

    /**
     * Assert toString() invocation works when given an "empty" 
     * UnknownRemoteObject (one without members)
     *
     * @access  public
     */
    #[@test]
    public function noMemberstoStringInvocation() {
      $this->assertEquals(
        "remote.UnknownRemoteObject@(Test) {\n}", 
        xp::stringOf(new UnknownRemoteObject('Test'))
      );
    }  

    /**
     * Assert toString() invocation works when given an UnknownRemoteObject
     * with exactly one member.
     *
     * @access  public
     */
    #[@test]
    public function oneMemberToStringInvocation() {
      $this->assertEquals(
        "remote.UnknownRemoteObject@(Test) {\n  [referenceId         ] 6100\n}", 
        xp::stringOf(new UnknownRemoteObject('Test', array('referenceId' => 6100)))
      );
    }

    /**
     * Assert toString() invocation works when given an UnknownRemoteObject
     * with more than one member.
     *
     * @access  public
     */
    #[@test, @ignore]
    public function multipleMembersToStringInvocation() {
      $this->assertEquals(
        "remote.UnknownRemoteObject@(Test) {\n".
        "  [referenceId         ] 6100\n".
        "  [topic               ] \"TestTopic\"\n".
        "  [power               ] util.Binford(61000)\n".
        "}", 
        xp::stringOf(new UnknownRemoteObject('Test', array(
          'referenceId' => 6100,
          'topic'       => 'TestTopic',
          'power'       => new Binford(61000)
        )))
      );
    }
    
    /**
     * Assert read access to a member fails
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function readMember() {
      $o= &new UnknownRemoteObject();
      $id= $o->id;
    }  

    /**
     * Assert write access to a member fails
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function writeMember() {
      $o= &new UnknownRemoteObject();
      $o->id= 1;
    }  

    /**
     * Assert method call fails
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function invokeMethod() {
      $o= &new UnknownRemoteObject();
      $o->method();
    }  
  }
?>
