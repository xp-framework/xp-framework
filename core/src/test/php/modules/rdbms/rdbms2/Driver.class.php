<?php
/* This file is part of the XP framework
 *
 * $Id$
 */

  $package= 'rdbms2';

  /**
   * Pluggable database module
   *
   */
  interface rdbms2·Driver {

  	/**
  	 * Creates a new connection
  	 *
  	 * @param   string dsn
  	 * @return  rdbms2.Connection
  	 */
  	public function newConnection($dsn);
  }
?>