<?php
/* This class is part of the XP framework
 *
 * $Id: XPException.class.php 8895 2006-12-19 11:54:21Z kiesel $
 */

  namespace lang;
 
 
  ::uses('lang.Throwable');
  
  /**
   * Exception
   *
   * @purpose  Base class for all other exceptions
   * @see      http://java.sun.com/docs/books/tutorial/essential/exceptions/definition.html
   * @see      http://jinx.swiki.net/352
   * @see      http://www.artima.com/designtechniques/exceptions.html
   * @see      http://www.artima.com/designtechniques/desexcept.html
   */
  class XPException extends Throwable {
     
  }
?>
