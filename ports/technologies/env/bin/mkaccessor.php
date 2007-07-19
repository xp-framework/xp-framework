#!/usr/local/bin/php
<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */

  require('lang.base.php');
  uses('io.sys.StdStream');
  
  // {{{ main
  $stdin= &StdStream::get(STDIN);
  $str= '';
  while (!$stdin->eof()) {
    $str.= $stdin->read();
  }
  
  echo $str."\n";

  $t= strtok($str, "\r\n");
  do {
    list($var, $init)= explode('=', $t, 2);
    $var= trim($var);
    $init= substr(trim($init), 0, -1);
    
    switch (strtolower($init)) {
      case 'null':
        $type= 'lang.Object';
        break;
        
      case "''":
        $type= 'string';
        break;

      case '0':
        $type= 'int';
        break;

      case '0.0':
        $type= 'float';
        break;

      case 'false':
      case 'true':
        $type= 'bool';
        break;

      case 'array()':
        $type= 'mixed[]';
        break;

      case 1 == preg_match('#([^\[]+)\[\]#', $init, $matches):
      
        // TBD: Calculate correct singular, e.g. entry / entries
        $name= rtrim(substr($var, 1), 's');
        printf(<<<__
    /**
     * Add an element to %2\$ss
     *
     * @param   %4\$s%3\$s %2\$s
     */
    public function add%1\$s(%4\$s\$%2\$s) {
      \$this->%2\$ss[]= %4\$s\$%2\$s;
    }

    /**
     * Get one %2\$s element by position. Returns NULL if the element 
     * can not be found.
     *
     * @param   int i
     * @return  %4\$s%3\$s
     */
    public function %4\$s%2\$sAt(\$i) {
      if (!isset(\$this->%2\$ss[\$i])) return NULL;
      return \$this->%2\$ss[\$i];
    }

    /**
     * Get number of %2\$ss
     *
     * @return  int
     */
    public function num%1\$ss() {
      return sizeof(\$this->%2\$ss);
    }

__
    , ucfirst($name), $name, $matches[1], '');
        continue 2;
        
      default:
        $type= 'mixed';
        break;
    }
    
    printf(<<<__
    /**
     * Set %2\$s
     *
     * @param   %3\$s %2\$s
     */
    public function set%1\$s(\$%2\$s) {
      \$this->%2\$s= \$%2\$s;
    }

    /**
     * Get %2\$s
     *
     * @return  %3\$s
     */
    public function get%1\$s() {
      return \$this->%2\$s;
    }


__
    , ucfirst(substr($var, 1)), substr($var, 1), $type);    
  } while ($t= strtok("\r\n"));
  
  // }}}
?>
