<?php
/* This class is part of the XP framework
 *
 * $Id: NoSuchEntityException.class.php 9347 2007-01-23 11:47:09Z friebe $ 
 */

  namespace rdbms::finder;

  uses('rdbms.finder.FinderException');

  /**
   * Indicates a specific entity could not be found
   *
   * @see      xp://rdbms.finder.FinderException#find
   * @purpose  Chained exception
   */
  class NoSuchEntityException extends FinderException {
  
  }
?>
