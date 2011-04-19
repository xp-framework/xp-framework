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
    public
      $beans= array();
      
    /**
     * Returns a list of all beans
     *
     * @return  remote.reflect.BeanDescription[]
     */
    public function beans() {
      return array_values($this->beans);
    }

    /**
     * Returns number of beans
     *
     * @return  int
     */
    public function size() {
      return sizeof($this->beans);
    }

    /**
     * Retrieve a single bean
     *
     * @param   string name
     * @return  remote.reflect.BeanDescription or NULL if nothing is found
     */
    public function bean($name) {
      if (!isset($this->beans[$name])) {
        return xp::null();
      }
      return $this->beans[$name];
    }
  }
?>
