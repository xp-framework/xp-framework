<?php
/* This class is part of the XP framework
 *
 * $Id: Ftok.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace io::sys;
 
  /**
   * Ftok
   *
   * <quote>
   * DESCRIPTION
   * The ftok() function returns a key based on path and id that is usable in 
   * subsequent calls to msgget(), semget() and shmget(). The path argument 
   * must be the pathname of an existing file that the process is able to 
   * stat().
   * 
   * The ftok() function will return the same key value for all paths that 
   * name the same file, when called with the same id value, and will return 
   * different key values when called with different id values or with paths 
   * that name different files existing on the same file system at the same 
   * time. It is unspecified whether ftok() returns the same key value when 
   * called again after the file named by path is removed and recreated with 
   * the same name.
   * </quote>
   * 
   * @ext      sem
   * @purpose  Provide a static class for getting System V IPC keys
   */
  class Ftok extends lang::Object {
    
    /**
     * Convert a pathname and a project identifier to a System V IPC key 
     *
     * Sample:
     * <code>
     *   $key= Ftok::get();
     *   $anotherKey= Ftok::get(2);
     *   $yetAnotherKey= Ftok::get(1, $_SERVER['PHP_SELF']);
     * </code>
     *
     * @param   int id default 1
     * @param   string path default __FILE__
     * @return  int key
     */
    public static function get($id= 1, $path= __FILE__) {
      return ftok($path, $id);
    }
  }
?>
