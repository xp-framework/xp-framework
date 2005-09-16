<?php
  class uwrp·fq {
    var
      $fd= NULL;

    function stream_open($path, $mode, $options, &$open) {
      $this->fd= fopen(
        strtr(substr($path, 8), '.', DIRECTORY_SEPARATOR).'.class.php', 
        $mode, 
        TRUE
      );
      return is_resource($this->fd);
    }
    
    function stream_read($count) {
      return fread($this->fd, $count);
    }

    function stream_eof() {
      return feof($this->fd);
    }
    
    function reflect($str) {
      return strtolower(strtr($str, '.', '·'));
    }
  }
?>
