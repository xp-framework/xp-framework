<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.Runnable');

  /**
   * Channel wrapper for php:// I/O streams stdin, stdout, stderr,
   * input and output.
   *
   * @see      xp://net.xp_framework.unittest.io.streams.ChannelStreamTest
   * @purpose  Stream wrapper
   */
  class ChannelWrapper extends Object {
    protected static 
      $streams = array();

    protected 
      $offset  = 0,
      $channel = NULL;
    
    /**
     * Capture output
     *
     * @param   lang.Runnable r
     * @param   array<string, string> initial
     * @return  array<string, string>
     */
    public static function capture(Runnable $r, $initial= array()) {
      self::$streams= $initial;
      stream_wrapper_unregister('php');
      stream_wrapper_register('php', __CLASS__);
      
      try {
        $r->run();
      } catch (Exception $t) { } finally(); {
        stream_wrapper_restore('php');
        if (isset($t)) throw $t;
      }
      
      return self::$streams;
    }
    
    /**
     * Callback for fopen
     *
     * @param   string path
     * @param   string mode
     * @param   int options
     * @param   string opened_path
     */
    public function stream_open($path, $mode, $options, $opened_path) {
      $channel= substr($path, strlen('php://'));
      if (!isset(self::$streams[$channel])) {
        self::$streams[$channel]= '';
      } else if (strstr($mode, 'w')) {
        $this->offset= strlen(self::$streams[$channel])- 1;
      } else {
        $this->offset= 0;
      }
      $this->channel= $channel;
      return TRUE;
    }

    /**
     * Callback for fclose
     *
     * @return  bool
     */
    public function stream_close() {
      return TRUE;
    }

    /**
     * Stream wrapper method stream_flush
     *
     * @return  bool
     */
    public function stream_flush() {
      return TRUE;
    }

    /**
     * Callback for fwrite
     *
     * @param   string data
     * @return  int length
     */
    public function stream_write($data) {
      self::$streams[$this->channel].= $data;
      $this->offset+= strlen($data);
    }
    
    /**
     * Callback for fread
     *
     * @param   int count
     * @return  string
     */
    public function stream_read($count) {
      $chunk= substr(self::$streams[$this->channel], $this->offset, $count);
      $this->offset+= strlen($chunk);
      return $chunk;
    }

    /**
     * Callback for feof
     *
     * @return  bool eof
     */
    public function stream_eof() {
      return $this->offset >= strlen(self::$streams[$this->channel])- 1;
    }
  }
?>
