<?php
  define('LOGGER_TYPE_INFO',    '[info]');
  define('LOGGER_TYPE_WARN',    '[warn]');
  define('LOGGER_TYPE_ERROR',   '[error]');
  define('LOGGER_TYPE_DEBUG',   '[debug]');
  define('LOGGER_TYPE_MARK',    '---------------------------------------------------------------------');
  
  class Logger extends Object {
    var 
      $_appenders= array();
  
    /**
     * Gibt eine Instanz zurück
     *
     * @return Logger Das Logger-Objekt
     */
    function &getInstance() {
      static $LOG__instance;
  
      if (!isset($LOG__instance)) {
        $LOG__instance= new Logger();
      }
      return $LOG__instance;
    }

    /**
     * Private Helper-Funktion
     *
     * @access private
     */
    function callAppenders() {
      $args= func_get_args();
      foreach ($this->_appenders as $appender) {
        call_user_func_array(
          array(&$appender, 'append'),
          $args
        );
      }
    }
    
    /**
     * Fügt einen Appender hinzu
     *
     * @param Appender appender Das Appender-Objekt
     */
    function addAppender(&$appender) {
      $this->_appenders[]= &$appender;
    }

    /**
     * Hängt einen Info-String an
     *
     * @param   mixed args Beliebige Variablen
     */
    function info() {
      $args= func_get_args();
      array_unshift($args, LOGGER_TYPE_INFO);
      call_user_func_array(
        array(&$this, 'callAppenders'),
        $args
      );
    }

    /**
     * Hängt einen Warn-String an
     *
     * @param   mixed args Beliebige Variablen
     */
    function warn() {
      $args= func_get_args();
      array_unshift($args, LOGGER_TYPE_WARN);
      call_user_func_array(
        array(&$this, 'callAppenders'),
        $args
      );
    }

    /**
     * Hängt einen Fehler-String an
     *
     * @param   mixed args Beliebige Variablen
     */
    function error() {
      $args= func_get_args();
      array_unshift($args, LOGGER_TYPE_ERROR);
      call_user_func_array(
        array(&$this, 'callAppenders'),
        $args
      );
    }

    /**
     * Hängt einen Trenner an
     *
     */
    function mark() {
      $this->callAppenders(LOGGER_TYPE_MARK);
    }

    /**
     * Hängt einen Debug-String an
     *
     * @param   mixed args Beliebige Variablen
     */
    function debug() {
      $args= func_get_args();
      array_unshift($args, LOGGER_TYPE_DEBUG);
      call_user_func_array(
        array(&$this, 'callAppenders'),
        $args
      );
    }
    
    /**
     * Hängt einen sprintf-String an
     *
     * @param   string printf Der sprintf-String
     * @param   array  args Die sprintf-Argumente
     */
    function appendf() {
      $arg= func_get_args();
      call_user_func_array(
        array(&$this, 'callAppenders'),
        vsprintf($arg[0], array_slice($arg, 1))
      );
    }
  }
?>
