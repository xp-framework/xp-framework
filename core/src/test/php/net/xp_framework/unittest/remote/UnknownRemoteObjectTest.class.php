<?php namespace net\xp_framework\unittest\remote;

use unittest\TestCase;
use remote\UnknownRemoteObject;
use util\Binford;


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
   */
  #[@test]
  public function noMemberstoStringInvocation() {
    $this->assertEquals(
      "remote.UnknownRemoteObject@(Test) {\n}", 
      \xp::stringOf(new UnknownRemoteObject('Test'))
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
      \xp::stringOf(new UnknownRemoteObject('Test', array('referenceId' => 6100)))
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
      \xp::stringOf(new UnknownRemoteObject('Test', array(
        'referenceId' => 6100,
        'topic'       => 'TestTopic',
        'power'       => new Binford(61000)
      )))
    );
  }
  
  /**
   * Assert read access to a member fails
   *
   */
  #[@test, @expect(class= 'lang.IllegalAccessException', withMessage= '/name::id/')]
  public function readMember() {
    $o= new UnknownRemoteObject('name');
    $id= $o->id;
  }  

  /**
   * Assert write access to a member fails
   *
   */
  #[@test, @expect(class= 'lang.IllegalAccessException', withMessage= '/name::id/')]
  public function writeMember() {
    $o= new UnknownRemoteObject('name');
    $o->id= 1;
  }  

  /**
   * Assert method call fails
   *
   */
  #[@test, @expect(class= 'lang.IllegalAccessException', withMessage= '/name::method/')]
  public function invokeMethod() {
    $o= new UnknownRemoteObject('name');
    $o->method();
  }  
}
