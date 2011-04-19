<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'net.xp_framework.unittest.scriptlet.rpc';
  
  /**
   * Value object
   *
   * @see      xp://net.xp_framework.unittest.scriptlet.rpc.XmlRpcDecoderTest
   * @purpose  Test
   */
  class net·xp_framework·unittest·scriptlet·rpc·ValueObject extends Object {
    public static
      $cache= NULL;
    
    public
      $name = '';
    
    protected
      $age  = 0;
    
    private
      $_new = FALSE;
    
    /**
     * Get name
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }
    
    /**
     * Set name
     *
     * @param  string name
     */
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get age
     *
     * @return  int
     */
    public function getAge() {
      return $this->age;
    }

    /**
     * Set age
     *
     * @param   int age
     */
    public function setAge($age) {
      $this->age= $age;
    }

    /**
     * Get whether this object is new
     *
     * @return  bool
     */
    public function isNew() {
      return $this->_new;
    }

    /**
     * Set whether this object is new
     *
     * @param   bool new
     */
    public function setNew($new) {
      $this->_new= $new;
    }
  }
?>
