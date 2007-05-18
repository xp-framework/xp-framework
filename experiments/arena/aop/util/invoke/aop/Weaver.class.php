<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Weave interceptors into sourcecode
   *
   * @purpose  AOP
   */
  class Weaver extends Object {
    private 
      $f     = NULL,
      $pc    = NULL,
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
      $this->f= fopen($uri, $mode);
      if (isset(Aspects::$pointcuts[$this->class])) {
        $this->pc= Aspects::$pointcuts[$this->class];
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
      if (FALSE === ($t= fgetcsv($this->f, 200, ' ', "\0"))) return FALSE;

      if ($this->pc) {
      
        // Search for "function" keyword
        if ($p= array_search('function', $t)) {
          sscanf(implode('', array_slice($t, $p+ 1)), '%[^(]%[^{]', $name, $args);
          
          // DEBUG fputs(STDERR, "NAME = # $this->class::$name::$args:: #\n");
          
          if (isset($this->pc[$name])) {
            $r= 'function '.$name.$args.' { ';
            $inv= 'call_user_func_array(Aspects::$pointcuts[\''.$this->class.'\'][\''.$name.'\']';

            // @before
            isset($this->pc[$name][BEFORE])
              ? $r.= $inv.'['.BEFORE.'], array'.$args.');'
              : TRUE
            ;

            // @except
            isset($this->pc[$name][THROWING])
              ? $r.= 'try { $r= $this->·'.$name.$args.'; } catch (Exception $e) { '.$inv.'['.THROWING.'], $e); throw $e; } '
              : $r.= '$r= $this->·'.$name.$args.';';
            ;

            // @after
            isset($this->pc[$name][AFTER])
              ?  $r.= $inv.'['.AFTER.'], $r);'
              : TRUE
            ;

            $r.= ' return $r; } function';

            $t[$p]= $r;
            $t[$p+ 1]= '·'.$t[$p+ 1];
          }
        }
        
        // DEBUG fputs(STDERR, implode(' ', $t)."\n");
      }
      
      return implode(' ', $t)."\n";
    }
    
    /**
     * Stream wrapper eof() implementation
     *
     * @return  bool
     */
    function stream_eof() {
      return feof($this->f);
    }
  }
?>
