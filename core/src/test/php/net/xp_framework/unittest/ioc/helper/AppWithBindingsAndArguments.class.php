<?php
/* This class is part of the XP framework
 *
 * $Id: Car.class.php 2991 2011-02-12 23:35:48Z mikey $
 */

  uses(
    'net.xp_framework.unittest.ioc.helper.AppTestBindingModuleOne',
    'net.xp_framework.unittest.ioc.helper.AppTestBindingModuleTwo'
  );

  /**
   * @purpose  Helper class for test cases.
   */
  class AppWithBindingsAndArguments extends Object {
    protected $arg, $object, $generic;

    /**
     * return list of bindings required for this command
     *
     * @return  ioc.module.BindingModule[]
     */
    public static function __bindings() {
      return array(new AppTestBindingModuleOne(),
                   new AppTestBindingModuleTwo()
             );
    }

    /**
     * sets object
     *
     * @param  lang.Object  $object
     */
    #[@inject]
    public function setObject(Object $object) {
      $this->object = $object;
    }

    /**
     * returns object
     *
     * @return  lang.Object
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * sets generic
     *
     * @param  lang.Generic  $generic
     */
    #[@inject]
    public function setGeneric(Generic $generic) {
      $this->generic = $generic;
    }

    /**
     * returns generic
     *
     * @return  lang.Generic
     */
    public function getGeneric()
    {
        return $this->generic;
    }

    /**
     * returns set project path
     *
     * @return  string
     */
    #[@inject(optional=true), @named('argv.0')]
    public function setArgument($arg) {
      $this->arg = $arg;
    }

    /**
     * returns the argument
     *
     * @return  string
     */
    public function getArgument() {
      return $this->arg;
    }
  }
?>
