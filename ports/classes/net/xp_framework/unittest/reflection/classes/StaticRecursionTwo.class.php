<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.unittest.reflection.classes.StaticRecursionOne');

  /**
   * Class that loads a class that loads this class inside its static 
   * initializer
   *
   * @see      xp://net.xp_framework.unittest.reflection.ClassLoaderTest#loadClassFileWithRecusionInStaticBlock
   * @purpose  Fixture
   */
  class StaticRecursionTwo extends StaticRecursionOne {
  }
?>
