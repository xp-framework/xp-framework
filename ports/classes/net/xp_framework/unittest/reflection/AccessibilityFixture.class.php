<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Fixture class for accessibility tests
   *
   * @see      xp://net.xp_framework.unittest.reflection.AccessibilityTest
   */
  class AccessibilityFixture extends Object {
    protected $target= 'Target';
    protected static $staticTarget= 'Target';

    /**
     * Constructor
     *
     */
    protected function __construct() { }

    /**
     * Target method
     *
     * @return  string
     */
    protected function target() { 
      return 'Invoked';
    }
 
    /**
     * Target method
     *
     * @return  string
     */
    protected static function staticTarget() { 
      return 'Invoked';
    }
   
    /**
     * Entry point: Invoke constructor
     *
     * @param   lang.XPClass
     * @return  net.xp_framework.unittest.reflection.AccessibilityFixture
     */
    public static function construct(XPClass $class) {
      return $class->getConstructor()->newInstance(array());
    }

    /**
     * Entry point: Invoke target method
     *
     * @param   lang.XPClass
     * @return  string
     */
    public static function invoke(XPClass $class) {
      return $class->getMethod('target')->invoke(new self());
    }

    /**
     * Entry point: Invoke staticTarget method
     *
     * @param   lang.XPClass
     * @return  string
     */
    public static function invokeStatic(XPClass $class) {
      return $class->getMethod('staticTarget')->invoke(NULL);
    }

    /**
     * Entry point: Read target member
     *
     * @param   lang.XPClass
     * @return  string
     */
    public static function read(XPClass $class) {
      return $class->getField('target')->get(new self());
    }

    /**
     * Entry point: Read staticTarget member
     *
     * @param   lang.XPClass
     * @return  string
     */
    public static function readStatic(XPClass $class) {
      return $class->getField('staticTarget')->get(NULL);
    }

    /**
     * Entry point: Write target member, then read it back
     *
     * @param   lang.XPClass
     * @return  string
     */
    public static function write(XPClass $class) {
      with ($s= new self(), $f= $class->getField('target')); {
        $f->set($s, 'Modified');
        return $f->get($s);
      }
    }

    /**
     * Entry point: Write target member, then read it back
     *
     * @param   lang.XPClass
     * @return  string
     */
    public static function writeStatic(XPClass $class) {
      with ($f= $class->getField('staticTarget')); {
        $f->set(NULL, 'Modified');
        return $f->get(NULL);
      }
    }
  }
?>
