<?php
  define('LOG_TYPE_INFO',    'info');
  define('LOG_TYPE_WARN',    'warn');
  define('LOG_TYPE_ERROR',   'error');
  define('LOG_TYPE_MARK',    '---------------------------------------------------------------------');

  /**
   * LOG-Klasse
   *
   * @access static
   * @deprecated (use util.log.Logger)
   */
  class LOG extends Object { 
  
    function LOG($filename) {
      global $PHP_SELF, $argv;
      
      $GLOBALS['LOG__format']= '%1$s %2$s %3$s %4$s';
      $GLOBALS['LOG__filename']= $filename;
      $GLOBALS['LOG__identifier']= basename(
        empty($PHP_SELF) ? $argv[0] : $PHP_SELF
      );
    }
    
    function setFormat($format) {
      $GLOBALS['LOG__format']= $format;
    }

    function _appendLog($msg, $type= LOG_INFO) {
      global $LOG__identifier, $LOG__filename;

      if (!isset($LOG__filename)) return;
      
      if (is_array($msg) || is_object($msg)) {
        ob_start();
        var_dump($msg);
        $msg= ob_get_contents();
        ob_end_clean();
      }
      
      // Log appenden
      $line= sprintf(
        $GLOBALS['LOG__format'],
        date('Y-m-d, H:i'),
        $LOG__identifier,
        $type,
        $msg
      );
      $fd= fopen($LOG__filename, 'a');
      fputs($fd, $line."\n");
      fclose($fd);
    }
    
    function mark() {
      LOG::_appendlog('', LOG_TYPE_MARK);
    }
    
    function warn($msg) {
      LOG::_appendLog($msg, LOG_TYPE_WARN);
    }
    
    function info($msg) {
      LOG::_appendLog($msg, LOG_TYPE_INFO);
    }
    
    function error($msg) {
      LOG::_appendLog($msg, LOG_TYPE_ERROR);
    }
  }
?>
