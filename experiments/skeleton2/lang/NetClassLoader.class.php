<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */
 
  uses(
    'lang.ClassLoader', 
    'peer.http.HttpConnection', 
    'lang.RuntimeError',
    'io.File'
  );
  
  /** 
   * Loads an XP class via HTTP
   * 
   * Usage:
   * <code>
   *   uses('lang.NetClassLoader', 'util.cmd.ParamString');
   *   
   *   // {{{ main
   *   $p= &new ParamString();
   *   if (2 != $p->count) {
   *     printf("Usage: %s <fully qualified class name>\n", $p->value(0));
   *     exit();
   *   }
   *   
   *   $e= &new NetClassLoader('http://sitten-polizei.de/php/classes/');
   *   try(); {
   *     $name= $e->loadClass($p->value(1));
   *   } if (catch('ClassNotFoundException', $e)) {
   *   
   *     // Class or dependency not found
   *     $e->printStackTrace();
   *     exit();
   *   }
   *   
   *   // Create an instance
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
        unlink($d->path.DIRECTORY_SEPARATOR.$entry);
      }
      $d->close();
      return TRUE;
    }
    
    /**
     * Uses wrapper
     *
     * @model   static
     * @access  public
     * @param   string codebase
     * @param   string prefix
     * @param   string cache
     * @param   string* class names
     * @return  bool
     */
    function uses() {
      $codebase= func_get_arg(0);
      $prefix= func_get_arg(1);
      $cache= func_get_arg(2);
     
      for ($i= 3, $c= func_num_args(); $i < $c; $i++) {
        $name= func_get_arg($i);
        
        // If we can't load the class of the net, try locally
        try {
          NetClassLoader::_load($codebase, $prefix, $cache, $name);
        } catch(FileNotFoundException $e) {
          if (!ClassLoader::loadClass($name)) {
            return FALSE;
          }
        }
      }
      
      return TRUE;
    }
    
    /**
     * Load a class
     *
     * @model   static
     * @access  protected
     * @param   string codebase
     * @param   string prefix
     * @param   string cache
     * @param   string className (FQCN)
     * @return  mixed string classname or FALSE if the class is not found
     */
    function _load($codebase, $prefix, $cache, $className) {
      $uri= $this->codebase.strtr($className, '.', '/').'.class.php';
      $cacheName= $cache.'/'.$prefix.strtr($uri, '/:', '__').'.class.php';
      $headers= array();
      
      $f= &new File($cacheName);
      $conn= &new HttpConnection($uri);
      if ($f->exists()) {
        $f->open(FILE_MODE_READ);
        $headers['If-Modified-Since']= substr($f->readLine(), 9, -3);
        $f->close();
      }
      
      $r= &$conn->get(NULL, $headers);
      $status= $r->getStatusCode();

      switch ($status) {
        case 200:
          $f->open(FILE_MODE_WRITE);
          $f->writeLine(sprintf('<?php // %s ?>', $r->getHeader('Last-Modified')));

          $uses= sprintf(
            'NetClassLoader::uses(\'%s\', \'%s\', \'%s\', ', 
            $codebase, 
            $prefix, 
            $cache
          );

          while ($buf= $r->readData()) {
            $f->write(preg_replace(
              '#uses[\t\s]*\(#',
              $uses,
              $buf
            ));
          }
          $f->close();
          break;

        case 304:
          // Not modified, use our local copy
          break;

        case 404:
          trigger_error($conn->request->getRequestString(), E_USER_NOTICE);
          trigger_error($r->toString(), E_USER_NOTICE);
          throw(new FileNotFoundException('File '.$conn->request->url->getPath().' not found'));
          break;

        default:
          trigger_error($conn->request->getRequestString(), E_USER_NOTICE);
          trigger_error($r->toString(), E_USER_NOTICE);
          throw(new FormatException('Unexpected HTTP status code '.$status.' ['.$r->message.']'));
          break;
      }
      
      // Try to include the file. Other classes are also loaded
      // off the net as we execute this (using NetClassLoader::uses())
      if (!include_once($f->uri)) {
        throw(new RuntimeError('Internal error ('.$f->uri.')'));
      }
      
      xp::registry('class.'.xp::reflect($className), $className);
      return xp::reflect($className);
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
     */
    function loadClass($className) {
      try {
        $name= NetClassLoader::_load(
          $this->codebase,
          $this->prefix,
          $this->cache,
          $className
        );
      } catch(XPException $e) {
        throw(new ClassNotFoundException(sprintf(
          "class '%s' not found\n  Codebase: %s\n  Cause   : %s {\n    %s\n  }\n",
          $className,
          $this->codebase,
          $e->getClassName(),
          $e->message
        )));
      }
      
      return $name;
    }
  }
?>
