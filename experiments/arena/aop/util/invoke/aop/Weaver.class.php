<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'text.StreamTokenizer', 
    'io.streams.FileInputStream', 
    'util.invoke.aop.ChunkTokenizer',
    'io.File'
  );

  /**
   * Weave interceptors into sourcecode
   *
   * @purpose  AOP
   */
  class Weaver extends Object {
    private 
      $f     = NULL,
      $class = NULL;
    
    
    static function __static() {
      stream_wrapper_register('weave', __CLASS__);
    }
    
    /**
     * Stream wrapper open() implementation
     *
     * @param   string path "weave://" classname "|" uri
     * @param   string mode
     * @param   int options
     * @param   string opened_path
     * @return  bool
     */
    function stream_open($path, $mode, $options, $opened_path) {
      sscanf($path, 'weave://%[^|]|%[^$]', $this->class, $uri);
      if (isset(Aop::$pointcuts[$this->class])) {
        $this->f= new StreamTokenizer(new FileInputStream(new File($uri)), " \r\n\t", TRUE);
      } else {
        $this->f= new ChunkTokenizer(fopen($uri, $mode));
        $this->class= NULL;   // Disable
      }
      return TRUE;
    }
    
    /**
     * Stream wrapper read() implementation
     *
     * @param   int count
     * @return  string
     */
    function stream_read($count) {
      $t= $this->f->nextToken();
      if ($this->class) {
        if ('function' === $t) {
          $ws= $this->f->nextToken();
          $name= $this->f->nextToken('(');
          $this->f->nextToken('(');
          // DEBUG fputs(STDERR, "NAME = # $this->class::$name #\n");
          if (!isset(Aop::$pointcuts[$this->class.'::'.$name])) {
            return $t.$ws.$name.'(';
          }

          $args= '('.$this->f->nextToken('{');
          $this->f->nextToken('{');
          $t= 'function '.$name.$args.'{ ';

          // @before
          $t.= 'call_user_func_array(Aop::$pointcuts[\''.$this->class.'::'.$name.'\'][\'before\'], array'.$args.');';

          // @except
          $t.= 'try { $r= $this->·'.$name.$args.'; } catch (Exception $e) { call_user_func(Aop::$pointcuts[\''.$this->class.'::'.$name.'\'][\'except\'], $e); throw $e; } ';

          // @after
          $t.= 'call_user_func(Aop::$pointcuts[\''.$this->class.'::'.$name.'\'][\'after\'], $r); return $r;';

          $t.= '} function ·'.$name.$args.' {';

          // DEBUG fputs(STDERR, $t."\n");
        }
      }
      return $t;
    }
    
    /**
     * Stream wrapper eof() implementation
     *
     * @return  bool
     */
    function stream_eof() {
      return $this->f->hasMoreTokens();
    }
  }
?>
