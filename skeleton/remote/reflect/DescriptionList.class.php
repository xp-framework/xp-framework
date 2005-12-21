<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * List of BeanDescription objects
   *
   * @see      xp://remote.reflect.BeanDescription
   * @purpose  Wrapper
   */
  class DescriptionList extends Object {
    var
      $beans= array();
      
    /**
     * Returns a list of all beans
     *
     * @access  public
     * @return  remote.reflect.BeanDescription[]
     */
    function beans() {
      return array_values($this->beans);
    }

    /**
     * Retrieve a single bean
     *
     * @access  public
     * @param   string name
     * @return  &remote.reflect.BeanDescription or NULL if nothing is found
     */
    function &bean($name) {
      if (!isset($this->beans[$name])) {
        return xp::null();
      }
      return $this->beans[$name];
    }
  }
?>
