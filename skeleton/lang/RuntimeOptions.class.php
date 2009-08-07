<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents runtime options
   *
   * @see      xp://lang.Runtime::startupOptions
   */
  class RuntimeOptions extends Object {
    protected $backing= array();
    
    /**
     * Parse command line, stopping at first argument without "-"
     * or at "--" (php [options] -- [args...])
     *
     * @param   string[] arguments
     * @return  lang.RuntimeOptions
     * @throws  lang.FormatException in case an unrecognized argument is encountered
     */
    public static function parse($arguments) {
      $self= new self();
      while (NULL !== ($argument= array_shift($arguments))) {
        if ('-' !== $argument{0} || '--' === $argument) break;
        switch ($argument{1}) {
          case 'q': {
            $self->backing["\0q"]= TRUE; 
            break;
          }
          case 'd': {
            sscanf($argument, "-d%[^=]=%[^\r]", $setting, $value); 
            $key= 'd'.ltrim($setting, ' ');
            if ('dinclude_path' === $key) {
              $self->backing[$key]= array(escapeshellarg(get_include_path()));
            } else if (!isset($self->backing[$key])) {
              $self->backing[$key]= array($value); 
            } else {
              $self->backing[$key][]= $value;
            }
            break;
          }
          default: throw new FormatException('Unrecognized argument "'.$argument.'"');
        }
      }
      return $self;
    }

    /**
     * Set switch (e.g. "-q")
     *
     * @param   string name switch name without leading dash
     * @return  lang.RuntimeOptions this object
     */
    public function withSwitch($name) {
      $this->backing["\0".$name]= TRUE; 
      return $this;
    }

    /**
     * Get switch (e.g. "-q")
     *
     * @param   string name switch name without leading dash
     * @param   bool default default FALSE
     * @return  bool
     */
    public function getSwitch($name, $default= FALSE) {
      $key= "\0".$name;
      return isset($this->backing[$key])
        ? $this->backing[$key]
        : $default
      ;
    }
    
    /**
     * Get setting (e.g. "include_path")
     *
     * @param   string name
     * @param   string[] default default NULL
     * @return  string[] values
     */
    public function getSetting($name, $default= NULL) {
      $key= 'd'.$name;
      return isset($this->backing[$key])
        ? $this->backing[$key]
        : $default
      ;
    }

    /**
     * Set setting (e.g. "include_path")
     *
     * @param   string setting
     * @param   var value either a number, a string or an array of either
     * @return  lang.RuntimeOptions this object
     */
    public function withSetting($setting, $value) {
      $this->backing['d'.$setting]= (array)$value; 
      return $this;
    }
    
    /**
     * Return an array suitable for passing to lang.Process' constructor
     *
     * @return  string[]
     */
    public function asArguments() {
      $s= array();
      foreach ($this->backing as $key => $value) {
        if ("\0" === $key{0}) {
          $s[]= '-'.substr($key, 1);
        } else {
          foreach ($value as $v) {
            $s[]= '-'.$key.'='.$v;
          }
        }
      }
      return $s;
    }
    
    /**
     * Creates a string representation of these options
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'@'.xp::stringOf($this->asArguments());
    }
  }
?>
