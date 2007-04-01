<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class ArchiveSelfRunner extends Object {

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public static function newInstance() {
      return new self();
    }

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function run($argv) {
      
      // The first argument is the name of the .xar, so remove it
      array_shift($argv);
      
      $cl= $this->getClass()->getClassLoader();
      
      if (sizeof($argv) >= 2 && '-class' == $argv[0]) {
        $className= array_shift($argv);
      } else {
      
        // Pull in optional dependency
        $prop= XPClass::forName('util.Properties')
          ->getMethod('fromString')
          ->invoke(NULL, array($cl->getResource('META-INF/runner.ini'))
        );
        
        $className= $prop->readString('main', 'class');
      }
      
      var_dump($className, $argv, array_merge(array($className), (array)$argv));
      
      return $cl->loadClass('util.cmd.Runner')->getMethod('main')->invoke(
        NULL,
        array(array_merge(array(__FILE__, $className), (array)$argv))
      );
    }  
  }
?>
