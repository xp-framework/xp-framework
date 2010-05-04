<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('security.password.StandardAlgorithm', 'util.NoSuchElementException');

  /**
   * Calculates password strength
   *
   * Usage
   * =====
   * <code>
   *   $score= PasswordStrength::getAlgorithm('standard')->strengthOf($password);
   * </code>
   *
   * Score meaning
   * =============
   * The score returned - a value between 0 and 100 - can be mapped as follows:
   * <ul>
   *   <li>0..20 - very weak</li>
   *   <li>20..40 - weak</li>
   *   <li>40..60 - ok</li>
   *   <li>60..80 - strong</li>
   *   <li>80..100 - very strong</li>
   * </ul>
   *
   * @see      xp://security.password.StandardAlgorithm
   * @purpose  Entry point class
   */
  class PasswordStrength extends Object {
    protected static
      $algorithms= array();
      
    static function __static() {
      self::$algorithms['standard']= XPClass::forName('security.password.StandardAlgorithm');
    }
      
    /**
     * Register an algorithm
     *
     * @param   string name
     * @param   lang.XPClass<security.password.Algorithm> impl
     * @throws  lang.IllegalArgumentException in case the given class is not an Algorithm 
     */
    public static function setAlgorithm($name, XPClass $impl) {
      if (!$impl->isSubclassOf('security.password.Algorithm')) {
        throw new IllegalArgumentException('Given argument is not an Algorithm class ('.xp::stringOf($impl).')');
      }
      self::$algorithms[$name]= $impl;
    }
    
    /**
     * Retrieve an algorithm by its name
     *
     * @param   string name
     * @return  security.password.Algorithm
     * @throws  util.NoSuchElementException in case no such algorithm is registered
     */
    public static function getAlgorithm($name) {
      if (!isset(self::$algorithms[$name])) {
        throw new NoSuchElementException('No such algorithm "'.$name.'"');
      }
      return self::$algorithms[$name]->newInstance();
    }    
  }
?>
