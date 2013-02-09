<?php
/* This file is part of the XP framework
 *
 * $Id$
 */

  $package= 'rdbms2';

  uses('peer.URL');

  /**
   * Pluggable database module
   *
   */
  class rdbms2·Drivers extends Object {
    public static $impl= array();

  	/**
  	 * Creates a new connection
  	 *
  	 * @param   string name
  	 * @param   rdbms2.Driver driver
  	 */
  	public static function register($name, $driver) {
      self::$impl[$name]= $driver;
    }

    /**
     * Creates a new connection
     *
     * @param   string dsn
     * @return  rdbms2.Connection
     */
    public static function newConnection($dsn) {
      $driver= create(new URL($dsn))->getScheme();
      if (!isset(self::$impl[$driver])) {
        throw new IllegalArgumentException('No driver named "'.$driver.'"');
      }
      return self::$impl[$driver]->newConnection($dsn);
    }
  }
?>