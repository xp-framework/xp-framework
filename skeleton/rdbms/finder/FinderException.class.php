<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

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
  class FinderException extends ChainedException {
  
  }
?>
