<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'org.apache.xml.generator.Dependency',
    'lang.ElementNotFoundException'
  );

  /**
   * Include dependency
   *
   * @purpose  Dependency
   * @deprecated
   */
  class IncludeDependency extends Dependency {

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function process($params) {
      if (!file_exists($this->name)) {
        return throw(new ElementNotFoundException($this->name));
      }
      return file_get_contents($this->name);
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function generate(&$generator) {
      $generator->processor->setXMLFile($this->name);

      // Run the XML/XSL transformation
      try(); {
        $generator->processor->run();
        $generator->save($this->name, $generator->processor->output());
      } if (catch('TransformerException', $e)) {
        var_dump($generator->processor);
        return throw($e);
      } if (catch('Exception', $e)) {
        return throw($e);
      } 

      return TRUE;
    }
  
  }
?>
