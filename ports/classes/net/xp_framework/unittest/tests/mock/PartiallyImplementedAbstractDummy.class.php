<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.unittest.tests.mock.IComplexInterface');

  /**
   * An abstract class for tests
   *
   * @purpose Mockery tests
   */
  abstract class PartiallyImplementedAbstractDummy extends Object implements IComplexInterface {

    /**
     * Constructor
     */
    public function __construct($param1, $param2="default") {

    }

    /**
     * An implementation of the foo method from IComplexInterface.
     * 
     * @return string
     */
    public function foo()  {
      return "IComplexInterface.foo";
    }

    /**
     * Abstract method introduced in this class.
     */
    public abstract function baz($a);

    /**
     * Some non-interface method.
     *
     * @return string
     */
    public function foobar() {
      return "PartiallyImplementedAbstractDummy.foobar";
    }
  }
?>