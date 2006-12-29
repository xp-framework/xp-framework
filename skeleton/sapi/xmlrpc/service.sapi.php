<?php
/* This file provides the XMLRPC service sapi for the XP framework
 * 
 * $Id$
 */

  uses('webservices.xmlrpc.rpc.XmlRpcRouter');
  
  define('EPREPEND_IDENTIFIER', "\6100");
  
  // {{{ final class sapi新oap新ervice
  class sapi新oap新ervice {
  
    // {{{ internal string fault(&lang.Throwable exception, string code)
    //     Convert an exception to XML
    static function fault($exception, $code) {
      $answer= new XmlRpcMessage();
      $answer->create('Server', 'Error');

      $answer->setFault($code, $exception->toString());
      
      header('Content-type: text/xml; charset=iso-8859-1');
      return (
        $answer->getDeclaration()."\n".
        $answer->getSource(0)
      );      
    }

    // {{{ internal string output(string buf)
    //     Output handler
    static function output($buf) {

      // Check for fatal errors
      if (FALSE !== ($p= strpos($buf, EPREPEND_IDENTIFIER))) {
        return sapi暖mlrpc新ervice::fault(
          new Error(str_replace(EPREPEND_IDENTIFIER, '', substr($buf, $p))),
          'xp.internalerror'
        );
      }

      return $buf;
    }
    // }}}
    
    // {{{ internal void except(Exception e)
    //     Exception handler
    static function except($e) {
      self::fault(
        $e instanceof XPException ? $e : new XPException($e->getMessage()), 
        xp.'uncaughtexception'
      );
    }
    // }}}
  }
  // }}}
  
  ini_set('html_errors', 0);
  ini_set('display_errors', 1);
  ini_set('error_prepend_string', EPREPEND_IDENTIFIER);

  set_exception_handler(array('sapi暖mlrpc新ervice', 'except'));
  ob_start(array('sapi暖mlrpc新ervice', 'output'));
?>
