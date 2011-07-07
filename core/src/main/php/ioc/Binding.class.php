<?php
/* This class is part of the XP framework
 *
 * $Id: Binding.class.php 2991 2011-02-12 23:35:48Z mikey $
 */

  /**
   * @purpose  Binds an interface to an implementation.
   */
  interface Binding {

    /**
     * set the name of the injection
     *
     * @param   string       $name
     * @return  ioc.Binding
     */
    public function named($name);

    /**
     * returns the created instance
     *
     * @param   string  $type
     * @param   string  $name
     * @return  mixed
     */
    public function getInstance($type, $name);

    /**
     * creates a unique key for this binding
     *
     * @return  string
     */
    public function getKey();
  }
?>