<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  define('BEFORE',   0x0001);
  define('THROWING', 0x0002);
  define('AFTER',    0x0004);

  /**
   * Aspects registry
   *
   * @purpose  Registry
   */
  class Aspects extends Object {
    public static $pointcuts= array();
    
    /**
     * Register an aspect
     *
     * @param   Generic aspect
     * @throws  lang.IllegalArgumentException if an error is encountered
     */
    public static function register(Generic $aspect) {
      $c= $aspect->getClass();
      if (!$c->hasAnnotation('aspect')) {
        throw new IllegalArgumentException('Class '.$c->toString().' does not have an @aspect annotation!');
      }

      foreach ($c->getMethods() as $m) {
        if ($m->hasAnnotation('pointcut')) {
          sscanf($m->getAnnotation('pointcut'), '%[^:]::%s', $classname, $method);
          @self::$pointcuts[$classname][$method]= array();
          $p[$m->getName()]= &self::$pointcuts[$classname][$method];
        } else if ($m->hasAnnotation('before')) {
          $p[$m->getAnnotation('before')][BEFORE]= array($aspect, $m->getName());
        } else if ($m->hasAnnotation('after')) {
          $p[$m->getAnnotation('after')][AFTER]= array($aspect, $m->getName());
        } else if ($m->hasAnnotation('throwing')) {
          $p[$m->getAnnotation('throwing')][THROWING]= array($aspect, $m->getName());
        }
      }
      if (empty(self::$pointcuts[$classname])) {
        unset(self::$pointcuts[$classname]);
        throw new IllegalArgumentException('Class '.$c->toString().' does not define any pointcuts!');
      }
    }
  }
?>
