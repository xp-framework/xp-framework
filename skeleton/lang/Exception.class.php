<?php
  // Genereller Fehler
  define('E_GENERAL_EXCEPTION',   0x0008);
  
  // Parameter, Format, Typ
  define('E_PARAM_EXCEPTION',     0xF001);
  define('E_FORMAT_EXCEPTION',    0xF002);
  define('E_TYPE_EXCEPTION',      0xF003);
  
  // Violations!!!
  define('E_ILLEGAL_ARGUMENT_EXCEPTION',        0xF100);
  define('E_ILLEGAL_STATE_EXCEPTION',           0xF101);
  define('E_CLASS_NOTFOUND_EXCEPTION',          0xF102);
    
  // IO
  define('E_IO_EXCEPTION',        0xF200);
  
  // Net
  define('E_CONNECT_EXCEPTION',   0xF300);
  
  // Irgendeine
  define('E_ANY_EXCEPTION',       0xFFFF);
  
  class Exception extends Object {
    var            
      $type,
      $message,
      $file,
      $line;                

    function Exception() {
      $this->type= $this->message= $this->file= $this->line= NULL;                            
    }
  }  
  
  function Exception__Handler($type, $message, $errfile, $errline) {
    global $exceptions;

    $exception= new Exception();
    $exception->type= $type;
    $exception->message= $message;
    $exception->file= $errfile;
    $exception->line= $errline;
    
    throw($type, $exception);
    return 0;
  }

  function try() {
    global $exceptions;
    
    $exceptions= array();
    $GLOBALS['Exception__errorHandler']= set_error_handler('Exception__Handler');
  }

  function catch($eType) {
    global $exceptions;
    
    // Restore
    set_error_handler($GLOBALS['Exception__errorHandler']); 
    
    // Keine Exceptions->OK:)
    if (sizeof($exceptions)== 0) return 0;

    // Gibt's eine Exception?
    $type= (E_ANY_EXCEPTION== $eType) ? key($exceptions): $eType;    
    if (!isset($exceptions[$type])) return 0;

    // Merken und vom Stack nehmen
    $exception= $exceptions[$type];
    unset($exceptions[$type]);
    return $exception;
  }

  function throw($type, $e) {
    global $exceptions;
    
    if (!is_object($e)) {
      Exception__Handler($type, $e, __FILE__, __LINE__);
    } else {
      $exceptions[$type]= $e;
      foreach ($GLOBALS['Exception__attachedHandlers'] as $handler) {
        $handler($e);
      }
    }
    return 0;
  }
  
  function attachHandler($funcName) {
    $GLOBALS['Exception__attachedHandlers'][]= $funcName;
  }
  
  $GLOBALS['Exception__attachedHandlers']= array();
?>
