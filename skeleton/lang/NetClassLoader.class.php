<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */
 
  uses('lang.ClassLoader', 'net.http.HTTPConnection', 'io.File');
  
  /** 
   * Loads an XP class via HTTP
   * 
   * Usage:
   * <code>
   *   $e= &new NetClassLoader('http://sitten-polizei.de/php/classes/%s.class.php');
   *   try(); {
   *     $name= $e->loadClass($_SERVER['argv'][1]);
   *   } if (catch('ClassNotFoundException', $e)) {
   *     die($e->printStackTrace());
   *   }
   * 
   *   $obj= &new $name();
   *   var_dump($obj, $obj->getClassName(), $obj->toString());
   * </code>
   *
   * @purpose  Load classes of the net
   * @see      xp://lang.ClassLoader
   */
  class NetClassLoader extends ClassLoader {
    var
      $codebase = '',
      $cache    = '';
    
    /**
     * Constructor
     * 
     * @access  public
     * @param   string codebase
     * @param   string cache default '/tmp' cache directory
     */
    function __construct($codebase, $cache= '/tmp') {
      $this->codebase= $codebase;
      $this->cache= $cache;
      $this->prefix= $this->getClassName().'-';
      parent::__construct();
    }
    
    /**
     * Expunge cache directory
     *
     * @access  public
     * @return  bool success
     */
    function expunge() {
      if (FALSE === ($d= dir($this->cache))) return FALSE;
      while ($entry= $d->read()) {
        if ($this->prefix != substr($entry, 0, strlen($this->prefix))) continue;
        unlink($d->path.'/'.$entry);
      }
      $d->close();
      return TRUE;
    }

    /**
     * Load a class via HTTP. The HTTP return status *must* either be 200 OK to
     * indicate success or 304 Not Modified to indicate the file has'nt changed
     * since it's last retreival
     *
     * @access  public
     * @param   string className fully qualified class name io.File
     * @return  string class' name for instantiation
     * @throws  ClassNotFoundException in case the class can not be found
     * @throws  FormatException in case there is a unexpected HTTP return code
     */
    function loadClass($className) {
      $uri= sprintf($this->codebase, $className);
      $cacheName= $this->cache.'/'.$this->prefix.strtr($uri, '/:', '__').'.class.php';
      
      $f= &new File($cacheName);
      $conn= &new HTTPConnection($uri);
      if ($f->exists()) {
        $f->open(FILE_MODE_READ);
        $conn->headers['If-Modified-Since']= substr($f->readLine(), 9, -3);
        $f->close();
      }
      
      try(); {
        $conn->get();

        switch ($conn->response->status) {
          case 200:
            $f->open(FILE_MODE_WRITE);
            $f->writeLine(sprintf('<?php // %s ?>', $conn->response->header['Last-Modified']));
            while ($buf= $conn->getResponse(FALSE)) {
              $f->write($buf);
            }
            $f->close();
            break;
            
          case 304:
            // Not modified, use our local copy
            break;

          default:
            throw(new FormatException('HTTP return code ['.$conn->response->status.'] != 200'));
        }
        
        // DEBUG var_dump($conn->response, $conn->request);
        
      } if (catch('Exception', $e)) {
        return throw(new ClassNotFoundException(sprintf(
          'class "%s" [codebase %s] not found: %s',
          $className,
          $this->codebase,
          $e->message
        )));
      }
      
      $result= include_once($f->uri);
      if (FALSE === $result) return throw(new ClassNotFoundException(sprintf(
        'class "%s" [codebase %s] not found',
        $className,
        $this->codebase
      )));

      $p= parse_url($this->codebase);
      $GLOBALS['php_class_names'][strtolower($className)]= (
        implode('.', array_reverse(explode('.', $p['host']))).
        '.'.
        ucfirst($className)
      );
      return $className;
    }
  }
?>
