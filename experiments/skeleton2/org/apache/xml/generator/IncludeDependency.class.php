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
   */
  class IncludeDependency extends Dependency {

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    public function process($params) {
      if (!file_exists($this->name)) {
        throw (new ElementNotFoundException($this->name));
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
    public function generate(&$generator) {
      $generator->processor->setXMLFile($this->name);

      // Run the XML/XSL transformation
      try {
        $generator->processor->run();
        $generator->save($this->name, $generator->processor->output());
      } catch (TransformerException $e) {
        var_dump($generator->processor);
        throw ($e);
      } catch (XPException $e) {
        throw ($e);
      } 

      return TRUE;
    }
  
  }
?>
