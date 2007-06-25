<?php
/* This file provides the SOAP service sapi for the XP framework
 * 
 * $Id$
 */

  uses('webservices.soap.rpc.SoapRpcRouter');
  
  define('EPREPEND_IDENTIFIER', "\6100");
  
  // {{{ final class sapi新oap新ervice
  class sapi新oap新ervice {
  
    // {{{ internal string fault(lang.Throwable exception, string code)
    //     Convert an exception to XML
    static function fault($exception, $code) {
      $answer= new XPSoapMessage();
      $answer->create();

      foreach ($exception->getStackTrace() as $element) {
        $stacktrace[]= $element->toString();
      }

      $answer->setFault(
        $code,
        $exception->getMessage(),
        getenv('SERVER_NAME').':'.getenv('SERVER_PORT'),
        $stacktrace
      );
      
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
        return sapi新oap新ervice::fault(
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
      header('HTTP/1.1 500 Internal Server Error');
      echo self::fault(
        $e instanceof XPException ? $e : new XPException($e->getMessage()), 
        HTTP_INTERNAL_SERVER_ERROR
      );
    }
    // }}}
  }
  // }}}
  
  ini_set('html_errors', 0);
  ini_set('display_errors', 1);
  ini_set('error_prepend_string', EPREPEND_IDENTIFIER);

  set_exception_handler(array('sapi新oap新ervice', 'except'));
  ob_start(array('sapi新oap新ervice', 'output'));
?>
