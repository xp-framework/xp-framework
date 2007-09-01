<?php
/* This class is part of the XP framework
 *
 * $Id: UnknownRemoteObjectTest.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace net::xp_framework::unittest::remote;

  ::uses(
    'unittest.TestCase',
    'remote.UnknownRemoteObject',
    'util.Binford'
  );

  /**
   * Unit test for UnknownRemoteObject class
   *
   * @see      xp://remote.UnknownRemoteObject
   * @purpose  TestCase
   */
  class UnknownRemoteObjectTest extends unittest::TestCase {

    /**
     * Assert toString() invocation works when given an "empty" 
     * UnknownRemoteObject (one without members)
     *
     */
    #[@test]
    public function noMemberstoStringInvocation() {
      $this->assertEquals(
        "remote.UnknownRemoteObject@(Test) {\n}", 
        ::xp::stringOf(new remote::UnknownRemoteObject('Test'))
      );
    }  

    /**
     * Assert toString() invocation works when given an UnknownRemoteObject
     * with exactly one member.
     *
     */
    #[@test]
    public function oneMemberToStringInvocation() {
      $this->assertEquals(
        "remote.UnknownRemoteObject@(Test) {\n  [referenceId         ] 6100\n}", 
        ::xp::stringOf(new remote::UnknownRemoteObject('Test', array('referenceId' => 6100)))
      );
    }

    /**
     * Assert toString() invocation works when given an UnknownRemoteObject
     * with more than one member.
     *
     */
    #[@test]
    public function multipleMembersToStringInvocation() {
      $this->assertEquals(
        "remote.UnknownRemoteObject@(Test) {\n".
        "  [referenceId         ] 6100\n".
        "  [topic               ] \"TestTopic\"\n".
        "  [power               ] util.Binford(61000)\n".
        "}", 
        ::xp::stringOf(new remote::UnknownRemoteObject('Test', array(
          'referenceId' => 6100,
          'topic'       => 'TestTopic',
          'power'       => new util::Binford(61000)
        )))
      );
    }
    
    /**
     * Assert read access to a member fails
     *
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function readMember() {
      $o= new remote::UnknownRemoteObject();
      $id= $o->id;
    }  

    /**
     * Assert write access to a member fails
     *
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function writeMember() {
      $o= new remote::UnknownRemoteObject();
      $o->id= 1;
    }  

    /**
     * Assert method call fails
     *
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function invokeMethod() {
      $o= new remote::UnknownRemoteObject();
      $o->method();
    }  
  }
?>
