<?php
/* This class is part of the XP framework
 *
 * $Id: XSLCallback.class.php 9061 2007-01-02 13:57:25Z kiesel $ 
 */

  namespace xml;

  /**
   * XSL callback class.
   *
   * @ext      dom
   * @see      php://xslt_registerphpfunctions
   * @purpose  Provide PHP callback functions on object instances
   */
  class XSLCallback extends lang::Object {
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
    public static function invoke() {
      $args= func_get_args();
      if (sizeof($args) < 2) throw (new lang::IllegalArgumentException(
        'Cannot call XSL callback with less than 2 arguments.'
      ));
      
      $name= array_shift($args);
      $method= array_shift($args);

      if (!isset(self::getInstance()->instances[$name])) throw (new lang::IllegalArgumentException(
        'No such registered XSL callback instance: "'.$name.'"'
      ));
      $instance= self::getInstance()->instances[$name];
      
      if (
        !$instance->getClass()->hasMethod($method) ||
        !$instance->getClass()->getMethod($method)->hasAnnotation('xslmethod')
      ) throw (new lang::IllegalArgumentException(
        'Instance '.$name.' does not have (xsl-accessible) method '.$method
      ));
      
      return $instance->getClass()->getMethod($method)->invoke($instance, $args);
    }
  }
?>
