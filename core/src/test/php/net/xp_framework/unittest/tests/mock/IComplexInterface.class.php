<?php
/* This interface is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.unittest.tests.mock.IEmptyInterface');

  /**
   * Dummy interface used in other tests
   *
   * @see      xp://unittest.mock.MockRepository
   * @purpose  Unit Test
   */
  interface IComplexInterface extends IEmptyInterface {
    function foo();
    function bar($a, $b);
    
    function getFoo();
    function setFoo($value);
    
    function fooWithTypeHint(IEmptyInterface $arg);
  }
?>