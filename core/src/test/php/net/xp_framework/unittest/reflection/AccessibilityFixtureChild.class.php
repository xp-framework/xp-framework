<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.unittest.reflection.AccessibilityFixture');

  /**
   * Fixture class for accessibility tests
   *
   * @see      xp://net.xp_framework.unittest.reflection.AccessibilityTest
   */
  class AccessibilityFixtureChild extends AccessibilityFixture {

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
     * Entry point: Invoke target method
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
     * Entry point: Write staticTarget member, then read it back
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
