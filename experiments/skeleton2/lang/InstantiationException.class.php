<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Thrown when an application tries to create an instance of a class using 
   * the newInstance method in class XPClass, but the specified class object 
   * cannot be instantiated because it is an interface, an abstract class or
   * the constructor is not public.
   *
   * @see      xp://lang.XPClass#newInstance()
   * @purpose  Exception
   */
  class InstantiationException extends XPException {
  
  }
?>
