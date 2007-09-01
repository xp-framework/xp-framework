<?php
/* This class is part of the XP framework
 *
 * $Id: Error.class.php 2343 2003-09-21 17:53:57Z friebe $
 */

  namespace lang;
 
 
  ::uses('lang.Throwable');
  
  /**
   * An Error is a subclass of Throwable that indicates serious problems 
   * that a reasonable application should not try to catch. Most such 
   * errors are abnormal conditions.
   *
   * @purpose  Base class for all other errors
   * @see      http://java.sun.com/docs/books/tutorial/essential/exceptions/definition.html
   * @see      http://jinx.swiki.net/352
   * @see      http://www.artima.com/designtechniques/exceptions.html
   * @see      http://www.artima.com/designtechniques/desexcept.html
   */
  class Error extends Throwable {
     
  }
?>
