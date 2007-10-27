<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Properties');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class ArchiveSelfRunner extends Object {
    public static
      $manifest   = NULL;
      
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
    protected function manifest() {
      if (NULL === self::$manifest) {
        self::$manifest= Properties::fromString(
          $this->getClass()
          ->getClassLoader()
          ->getResource('META-INF/runner.ini')
        );
      }
      
      return self::$manifest;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    protected function classToRun(&$argv) {
      if (sizeof($argv) >= 2 && '-class' == $argv[0]) {
        $className= $argv[1];
        array_shift($argv);
        array_shift($argv);
        return $className;
      }
      
      return $this->manifest()->readString('main', 'class');
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
      
      $className= $this->classToRun($argv);
      var_dump($className, $argv, array_merge(array($className), (array)$argv));
      
      return $this->getClass()
        ->getClassLoader()
        ->loadClass('util.cmd.Runner')
        ->getMethod('main')
        ->invoke(
          NULL,
          array(array_merge(array(__FILE__, $className), (array)$argv))
      );
    }
  }
?>
