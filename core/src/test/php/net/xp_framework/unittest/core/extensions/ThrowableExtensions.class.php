<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  
  $package= 'net.xp_framework.unittest.core.extensions';

  /**
   * Throwable extension methods
   *
   * @see   https://gist.github.com/2168311
   * @see   xp://net.xp_framework.unittest.core.extensions.ExtensionInvocationTest
   */
  class net·xp_framework·unittest·core·extensions·ThrowableExtensions extends Object {

    static function __import($scope) {
      xp::extensions(__CLASS__, $scope);
    }

    /**
     * Clears stacktrace
     *
     * @param   lang.Throwable self
     */
    public static function clearStackTrace(Throwable $self) {
      $self->trace= array();
    }
  }
?>
