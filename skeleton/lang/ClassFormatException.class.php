<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.ClassLoadingException');

  /**
   * Indicates a class format error - for example:
   * <ul>
   *   <li>Class file does not declare any classes</li>
   *   <li>Class file does not declare class by file name</li>
   * </ul>
   *
   * @see      xp://lang.ClassLoader#loadClass
   * @purpose  Exception
   */
  class ClassFormatException extends XPException implements ClassLoadingException {
    
  }
?>
