<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  /**
   * Creates dependencies
   *
   * @purpose  Factory
   */
  class DependencyFactory extends Object {

    /**
     * Create a dependency
     *
     * @model   static
     * @access  public
     * @param   string scheme
     * @param   string name
     * @param   array config
     * @return  &org.apache.xml.generator.Dependency
     * @throws  lang.IllegalArgumentException
     */  
    function factory($scheme, $name, $config) {
      static $c= array();
      
      // Load class for scheme if not already done
      if (!isset($c[$scheme])) {
        try(); {
          // DEBUG printf("===> Loading Dependency class for >%s<\n", $scheme);
          $c[$scheme]= XPClass::forName($config[$scheme.'.class']);
        } if (catch('ClassNotFoundException', $e)) {
          return throw(new IllegalArgumentException(
            'Dependencytype "'.$scheme.'" not supported: '.$e->getMessage()
          ));
        }
      } else {
        // DEBUG printf("---> Already have Dependency class for >%s<\n", $scheme);
      }
      
      // Return an instance
      // DEBUG printf("---> Instantiation of Dependency class %s w/ %s\n", $c[$scheme]->getName(), $name);
      return $c[$scheme]->newInstance($name);
    }
  }
?>
