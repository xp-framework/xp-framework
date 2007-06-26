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
  class RemoteSessionConstants extends Object {
  
    // Global
    const INIT    = 0x0000;
    const CREATE  = 0x0001;
    const VALID   = 0x0002;
    const KILL    = 0x0003;
    const RESET   = 0x0004;

    // Session
    const EXISTS  = 0x0010;
    const WRITE   = 0x0011;
    const READ    = 0x0012;
    const DELETE  = 0x0013;
    const KEYS    = 0x0014;
    
    // Responses
    const ERROR   = 0x0020;
    const STATUS  = 0x0021;
    const VALUE   = 0x0022;

  }
?>
