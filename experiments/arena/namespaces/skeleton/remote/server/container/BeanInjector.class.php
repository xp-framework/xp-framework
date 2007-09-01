<?php
/* This class is part of the XP framework
 *
 * $Id: BeanInjector.class.php 9299 2007-01-16 12:13:26Z kiesel $ 
 */

  namespace remote::server::container;

  /**
   * Injector class to inject various resources into
   * a bean.
   *
   * @purpose  Inject resources
   */
  class BeanInjector extends lang::Object {

    /**
     * Fetch DB connection resource
     *
     * @param   string name
     * @return  rdbms.DBConnection
     */
    function getDBConnection($name) { 
      return rdbms::ConnectionManager::getInstance()->getByHost($name, 0);
    }

    /**
     * Fetch Properties resource
     *
     * @param   string name
     */
    function getProperties($name) { 
      return util::PropertyManager::getInstance()->getProperties($name);
    }

    /**
     * Fetch LogCategory resource
     *
     * @param   string name
     */
    function getLogCategory($name) { 
      return util::log::Logger::getInstance()->getCategory($name);
    }

    /**
     * Fetch resource by type and name
     *
     * @param   string type
     * @param   string name
     * @return  mixed
     */
    function injectFor($type, $name) {
      $typemethods= array(
        'rdbms.DBConnection'    => 'getDBConnection',
        'util.Properties'       => 'getProperties',
        'util.log.LogCategory'  => 'getLogCategory'
      );

      if (!isset($typemethods[$type])) throw new lang::IllegalArgumentException('Unknown injection type "'.$type.'"');
      return call_user_func_array(array($this, $typemethods[$type]), $name);
    }

  }
?>
