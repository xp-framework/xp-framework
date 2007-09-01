<?php
/* This class is part of the XP framework
 *
 * $Id: NoSuchElementException.class.php 8895 2006-12-19 11:54:21Z kiesel $
 */

  namespace util;
 
  /**
   * Thrown by the next method of an Iterator to indicate that 
   * there are no more elements.
   *
   * @purpose  Exception
   * @see      xp://util.Iterator
   */
  class NoSuchElementException extends lang::XPException {
  }
?>
