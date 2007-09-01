<?php
/* This class is part of the XP framework
 *
 * $Id: FinderException.class.php 10977 2007-08-27 17:14:26Z friebe $ 
 */

  namespace rdbms::finder;

  uses('lang.ChainedException');

  /**
   * Indicates an exception occured while using the Finder API. All
   * methods will wrap exceptions into an instance of this class or
   * a subclass of it. The causing exception is available via the 
   * getCause() method.
   *
   * @see      xp://lang.ChainedException
   * @purpose  Chained exception
   */
  class FinderException extends lang::ChainedException {
  
  }
?>
