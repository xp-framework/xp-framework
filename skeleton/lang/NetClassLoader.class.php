<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */
 
  uses('lang.ClassLoader', 'net.http.HTTPConnection', 'io.File');
  
  /** 
   * Loads an "external" class not belonging to the XP framework
   * 
   * Usage:
   * <code>
   *   $e= &new NetClassLoader('http://binford6100.info/classes');
   *   try(); {
   *     $name= $e->loadClass($argv[1]);
   *   } if (catch('ClassNotFoundException', $e)) {
   *     die($e->printStackTrace());
   *   }
   *
   *   $obj= &new $name();
   * </code>
   *
   * @access    public, static
   */
  class NetClassLoader extends ClassLoader {
    var
      $codebase= '',
      $cache=    '';
    
    /**
     * Constructor
     * 
     * @access  public
     * @param   string codebase
     */
    function __construct($codebase, $cache= '/tmp') {
      $this->codebase= $codebase;
      $this->cache= $cache;
      parent::__construct();
    }

    /**
     * Destructor
     *
     * @access  public
     */    
    function __destruct() {
      $d= dir($this->cache);
      $prefix= $this->getName().'-'.getmypid().'-';
      while ($entry= $d->read()) {
        if ($prefix != substr($entry, 0, strlen($prefix))) continue;
        unlink($d->path.'/'.$entry);
      }
      $d->close();
      parent::__destruct();
    }
    
    /**
     * Load
     *
     * @access  static
     * @param   string className fully qualified class name io.File
     * @return  string class' name for instantiation
     * @throws  ClassNotFoundException in case the class can not be found
     */
    function loadClass($className, $codebase= '', $cache= '/tmp') {
      if (isset($this)) {
        $codebase= $this->codebase;
        $cache= $this->cache;
      }
      
      $uri= sprintf($codebase, $className);
      $cacheName= $cache.'/'.$this->getName().'-'.getmypid().'-'.strtr($uri, '/:', '__').'.class.php';
      $f= &new File($cacheName);
      $conn= &new HTTPConnection($uri);
      if ($f->exists()) {
        $conn->headers['If-Modified-Since']= implode('', file($cacheName.'.mod'));
      }
      
      try(); {
        $conn->get();

        switch ($conn->response->status) {
          case 200:
            $f->open(FILE_MODE_WRITE);
            while ($buf= $conn->getResponse(FALSE)) {
              $f->write($buf);
            }
            $f->close();
            
            $mod= &new File($cacheName.'.mod');
            $mod->open(FILE_MODE_WRITE);
            $mod->write($conn->response->header['Last-Modified']);
            $mod->close();
            
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
          $codebase,
          $e->message
        )));
      }
      
      $result= include_once($f->uri);
      if (FALSE === $result) return throw(new ClassNotFoundException(sprintf(
        'class "%s" [codebase %s] not found',
        $className,
        $codebase
      )));

      $p= parse_url($codebase);
      $parts= explode('.', $p['host']);
      for ($i= sizeof($parts), $str= ''; $i > -1; $i--) {
        $str.= $parts[$i].'.';
      }
      $GLOBALS['php_class_names'][strtolower($className)]= substr($str, 1).ucfirst($className);
      return $className;
    }
  }
?>
