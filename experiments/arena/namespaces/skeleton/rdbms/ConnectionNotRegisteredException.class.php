<?php
/* This class is part of the XP framework
 *
 * $Id: ConnectionNotRegisteredException.class.php 2112 2003-08-25 11:30:17Z friebe $ 
 */

  namespace rdbms;

  uses('rdbms.SQLException');

  /**
   * Indicates a connection is not registered.
   *
   * @purpose  Exception
   */
  class ConnectionNotRegisteredException extends SQLException {
  
  }
?>
