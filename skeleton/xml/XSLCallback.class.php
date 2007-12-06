<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * XSL callback class.
   *
   * @ext      dom
   * @test     xp://net.xp_framework.unittest.xml.XslCallbackTest
   * @see      php://xslt_registerphpfunctions
   * @purpose  Provide PHP callback functions on object instances
   */
  class XSLCallback extends Object {
    private
      $instances  = array();  

    private static      
      $instance   = NULL;
      
    static function __static() {
      self::$instance= new self();
    }
    
    /**
     * Retrieve instance
     *
     * @return  xml.XSLCallback
     */
    public static function getInstance() {
      return self::$instance;
    }
    
    /**
     * Register new instance
     *
     * @param   string name
     * @param   lang.Object instance
     */
    public function registerInstance($name, $instance) {
      $this->instances[$name]= $instance;
    }
    
    /**
     * Remove all registered instances
     *
     */
    public function clearInstances() {
      $this->instances= array();
    }
    
    /**
     * Invoke method on a registered instance.
     *
     * @param   string instancename
     * @param   string methodname
     * @param   mixed* method arguments
     * @return  mixed
     * @throws  lang.IllegalArgumentException if the instance is not known
     * @throws  lang.IllegalArgumentException if the given method does not exist or is not xsl-accessible
     */
    public static function invoke($name, $method) {
      if (!isset(self::$instance->instances[$name])) throw new IllegalArgumentException(
        'No such registered XSL callback instance: "'.$name.'"'
      );

      $instance= self::$instance->instances[$name];
      if (
        !($m= $instance->getClass()->getMethod($method)) ||
        !($m->hasAnnotation('xslmethod'))
      ) throw new IllegalArgumentException(
        'Instance "'.$name.'" does not have (xsl-accessible) method "'.$method.'"'
      );
      
      $args= func_get_args();
      return call_user_func_array(array($instance, $method), array_slice($args, 2));
    }
  }
?>
