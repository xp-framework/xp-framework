<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
  uses('lang.ClassNotFoundException');

  /**
   * Indicates a class specified by a name cannot be found - that is,
   * no classloader provides such a class.
   *
   * @see   xp://lang.IClassLoader#loadClass
   * @see   xp://lang.XPClass#forName
   */
  class ClassDependencyException extends ClassNotFoundException {

    /**
     * Returns the exception's message - override this in
     * subclasses to provide exact error hints.
     *
     * @return  string
     */
    protected function message() {
      return 'Exception %s (Class "%s" could not be found)';
    }

  }
?>
