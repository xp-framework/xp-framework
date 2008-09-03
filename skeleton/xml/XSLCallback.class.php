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
     * @throws  lang.ElementNotFoundException if the given method does not exist or is not xsl-accessible
     */
    public static function invoke($name, $method) {
      if (!isset(self::$instance->instances[$name])) throw new IllegalArgumentException(
        'No such registered XSL callback instance: "'.$name.'"'
      );

      $instance= self::$instance->instances[$name];
      
      if (!($instance->getClass()->getMethod($method)->hasAnnotation('xslmethod'))) {
        throw new ElementNotFoundException(
          'Instance "'.$name.'" does not have method "'.$method.'"'
        );
      }
      
      $va= func_get_args();
      
      // Decode arguments [2..*]
      for ($i= 2, $args= array(), $s= sizeof($va); $i < $s; $i++) {
        $args[]= is_string($va[$i]) ? utf8_decode($va[$i]) : $va[$i];
      }
      
      // Call callback method
      $r= call_user_func_array(array($instance, $method), $args);
      
      // Encode result if necessary
      return is_string($r) ? utf8_encode($r) : $r;
    }
  }
?>
