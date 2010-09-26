<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'xp.compiler.emit.source';

  /**
   * Source buffer
   *
   */
  class xp·compiler·emit·source·Buffer extends Object {
    protected $source= '';
    public $line= 1;
    
    /**
     * Creates a new source buffer
     *
     * @param   string initial
     */
    public function __construct($initial= '', $line= 1) {
      $this->source= $initial;
      $this->line= $line + substr_count($initial, "\n");
    }
    
    /**
     * Append source and return this buffer
     *
     * @param   string arg
     * @return  xp.compiler.emit.source.Buffer this
     */
    public function append($arg) {
      $this->source.= $arg;
      $this->line+= substr_count($arg, "\n");
      return $this;
    }

    /**
     * Insert source at a given position and return this buffer
     *
     * @param   string arg
     * @param   int pos
     * @return  xp.compiler.emit.source.Buffer this
     */
    public function insert($arg, $pos) {
      $this->source= substr($this->source, 0, $pos).$arg.substr($this->source, $pos);
      $this->line+= substr_count($arg, "\n");
      return $this;
    }
    
    /**
     * Marks current position for insertion operations
     *
     * @return  int
     */
    public function mark() {
      return strlen($this->source);
    }

    /**
     * Set position
     *
     * @param   int[2] p
     */
    public function position($p) {
      $diff= $p[0] - $this->line;
      if ($diff > 0) {
        $this->source.= str_repeat("\n", $diff);
        $this->line= $p[0];
      }
      // if ($diff < 0) {
      //   echo 'At line '.$this->line.' request for line #'.$p[0]."\n";
      //   foreach (explode("\n", $this->source) as $i => $line) {
      //     printf("%-4d %s\n", $i, $line);
      //   }
      //   
      //   echo new IllegalStateException('Have gone too far');
      // }
    }

    /**
     * Replace a search string with a replacement and return this buffer
     *
     * @param   string search
     * @param   string replace
     * @return  xp.compiler.emit.source.Buffer this
     */
    public function replace($search, $replace) {
      $this->source= str_replace($search, $replace, $this->source);
      return $this;
    }
    
    /**
     * String cast overloading
     *
     * @return  string
     */
    public function __toString() {
      return $this->source;
    }
  }
?>
