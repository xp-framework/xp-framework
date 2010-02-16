<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Injector class to inject various resources into
   * a bean.
   *
   * @purpose  Inject resources
   */
  class BeanInjector extends Object {

    /**
     * Fetch DB connection resource
     *
     * @param   string name
     * @return  rdbms.DBConnection
     */
    function getDBConnection($name) { 
      return ConnectionManager::getInstance()->getByHost($name, 0);
    }

    /**
     * Fetch Properties resource
     *
     * @param   string name
     */
    function getProperties($name) { 
      return PropertyManager::getInstance()->getProperties($name);
    }

    /**
     * Fetch LogCategory resource
     *
     * @param   string name
     */
    function getLogCategory($name) { 
      return Logger::getInstance()->getCategory($name);
    }

    /**
     * Fetch resource by type and name
     *
     * @param   string type
     * @param   string name
     * @return  var
     */
    function injectFor($type, $name) {
      $typemethods= array(
        'rdbms.DBConnection'    => 'getDBConnection',
        'util.Properties'       => 'getProperties',
        'util.log.LogCategory'  => 'getLogCategory'
      );

      if (!isset($typemethods[$type])) throw new IllegalArgumentException('Unknown injection type "'.$type.'"');
      return call_user_func_array(array($this, $typemethods[$type]), $name);
    }

  }
?>
