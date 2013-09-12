<?php namespace net\xp_framework\unittest\reflection\classes;



/**
 * Class that loads a class that loads this class inside its static 
 * initializer
 *
 * @see      xp://net.xp_framework.unittest.reflection.ClassLoaderTest#loadClassFileWithRecusionInStaticBlock
 * @purpose  Fixture
 */
class StaticRecursionTwo extends StaticRecursionOne {
}
