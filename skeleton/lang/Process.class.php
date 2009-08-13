<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.File');

  /**
   * Process
   *
   * Example (get uptime information on a *NIX system)
   * <code>
   *   $p= new Process('uptime');
   *   $uptime= $p->out->readLine();
   *   $p->close();
   *
   *   var_dump($uptime);
   * </code>
   *
   * @test     xp://net.xp_framework.unittest.core.ProcessTest
   * @see      xp://lang.Runtime#getExecutable
   * @see      php://proc_open
   * @purpose  Execute external programs
   */
  class Process extends Object {
    public
      $in     = NULL,
      $out    = NULL,
      $err    = NULL,
      $exitv  = -1;
    
    protected static
      $escape = '';

    protected
      $_proc  = NULL,
      $status = array();
    
    static function __static() {
      $e= escapeshellarg('');
      self::$escape= (0 === strlen($e) ? '"' : $e{0});
    }
    
    /**
     * Escape and argument
     *
     * @param   string arg
     * @return  string escaped
     */
    protected function escape($arg) {
      return strstr($arg, ' ') && !strstr($arg, self::$escape) ? escapeshellarg($arg) : $arg;
    }

    /**
     * Constructor
     *
     * @param   string command default NULL
     * @param   string[] arguments default []
     * @param   string cwd default NULL the working directory
     * @param   array<string, string> default NULL the environment
     * @throws  io.IOException in case the command could not be executed
     */
    public function __construct($command= NULL, $arguments= array(), $cwd= NULL, $env= NULL) {
      static $spec= array(
        0 => array('pipe', 'r'),  // stdin
        1 => array('pipe', 'w'),  // stdout
        2 => array('pipe', 'w')   // stderr
      );
      
      // For `new self()` used in getProcessById()
      if (NULL === $command) return;

      // Check whether the given command is executable.
      $binary= $this->resolve($command);
      if (!is_file($binary) || !is_executable($binary)) {
        throw new IOException('Command "'.$binary.'" is not an executable file');
      }
      
      // Build command line
      $cmd= $this->escape($command);
      foreach ($arguments as $arg) {
        $cmd.= ' '.$this->escape($arg);
      }

      // Open process
      if (!is_resource($this->_proc= proc_open($cmd, $spec, $pipes, $cwd, $env, array('bypass_shell' => TRUE)))) {
        throw new IOException('Could not execute "'.$cmd.'"');
      }

      $this->status= proc_get_status($this->_proc);
      $this->status['exe']= $binary;
      $this->status['arguments']= NULL;

      // Assign in, out and err members
      $this->in= new File($pipes[0]);
      $this->out= new File($pipes[1]);
      $this->err= new File($pipes[2]);
    }

    /**
     * Create a new instance of this process.
     *
     * @param   string[] arguments default []
     * @param   string cwd default NULL the working directory
     * @param   array<string, string> default NULL the environment
     * @throws  io.IOException in case the command could not be executed
     */
    public function newInstance($arguments= array(), $cwd= NULL, $env= NULL) {
      return new self($this->status['exe'], $arguments, $cwd, $env);
    }

    /**
     * Resolve path for a command
     *
     * @param   string command
     * @return  string executable
     * @throws  io.IOException in case the command could not be found
     */
    public function resolve($command) {
      clearstatcache();
      
      // PATHEXT is in form ".{EXT}[;.{EXT}[;...]]"
      $extensions= array('') + explode(PATH_SEPARATOR, getenv('PATHEXT'));
    
      // If the command is in fully qualified form and refers to a file
      // that does not exist (e.g. "C:\DoesNotExist.exe", "\DoesNotExist.com"
      // or /usr/bin/doesnotexist), do not attempt to search for it.
      if (
        (strncasecmp(PHP_OS, 'Win', 3) === 0 && ':' === $command{1}) || 
        (DIRECTORY_SEPARATOR === $command{0})
      ) {
        foreach ($extensions as $ext) {
          $q= $command.$ext;
          
          if (file_exists($q) && !is_dir($q)) return realpath($q);
        }
        throw new IOException('"'.$command.'" does not exist');
      }

      // Check the PATH environment setting for possible locations of the 
      // executable if its name is not a fully qualified path name.
      $paths= explode(PATH_SEPARATOR, getenv('PATH'));
      foreach ($paths as $path) {
        foreach ($extensions as $ext) {
          $q= $path.DIRECTORY_SEPARATOR.$command.$ext;
          
          if (file_exists($q) && !is_dir($q)) return realpath($q);
        }
      }
      
      throw new IOException('Could not find "'.$command.'" in path');
    }

    /**
     * Get a process by process ID
     *
     * @param   int pid process id
     * @param   string exe
     * @return  lang.Process
     * @throws  lang.IllegalStateException
     */
    public static function getProcessById($pid, $exe= NULL) {
      $self= new self();
      $self->status= array(
        'pid'       => $pid, 
        'running'   => TRUE,
        'exe'       => $exe,
        'command'   => '',
        'arguments' => NULL
      );
      
      // Determine executable and command line:
      // * On Windows, use Windows Management Instrumentation API - see
      //   http://en.wikipedia.org/wiki/Windows_Management_Instrumentation
      //
      // * On systems with a /proc filesystem, use information from /proc/self
      //   See http://en.wikipedia.org/wiki/Procfs
      //
      // * Fall back to use the "_" environment variable and /bin/ps to retrieve
      //   the command line (please note unfortunately any quote signs have been 
      //   lost and it can thus be only used for display purposes)
      //
      // Note: It would be really nice to have a getmyexe() function in PHP
      // complementing getmypid().
      if (strncasecmp(PHP_OS, 'Win', 3) === 0) {
        try {
          $c= new com('winmgmts:');
          $p= $c->get('//./root/cimv2:Win32_Process.Handle="'.$pid.'"');
          $self->status['exe']= $p->executablePath;
          $self->status['command']= $p->commandLine;
        } catch (Exception $e) {
          throw new IllegalStateException('Cannot find executable: '.$e->getMessage());
        }
      } else if (file_exists($proc= '/proc/'.$pid)) {
        foreach (array('/exe', '/file') as $alt) {
          if (!file_exists($proc.$alt)) continue;
          $self->status['exe']= readlink($proc.$alt);
          break;
        }
        $self->status['command']= strtr(file_get_contents($proc.'/cmdline'), "\0", ' ');
      } else if ($exe) {
        try {
          $self->status['exe']= $self->resolve($exe);
          $self->status['command']= exec('ps -p '.$pid.' -ocommand');
        } catch (IOException $e) {
          throw new IllegalStateException($e->getMessage());
        }
      } else if ($_= getenv('_')) {
        $self->status['exe']= $self->resolve($_);
        $self->status['command']= exec('ps -p '.$pid.' -ocommand');
      } else {
        throw new IllegalStateException('Cannot find executable');
      }
      
      $self->in= xp::null();
      $self->out= xp::null();
      $self->err= xp::null();
      return $self;
    }
    
    /**
     * Get process ID
     *
     * @return  int
     */
    public function getProcessId() {
      return $this->status['pid'];
    }
    
    /**
     * Get filename of executable
     *
     * @return  string
     */
    public function getFilename() {
      return $this->status['exe'];
    }

    /**
     * Get command line
     *
     * @return  string
     */
    public function getCommandLine() {
      return $this->status['command'];
    }
    
    /**
     * Parse command line arguments
     *
     * @see     xp://lang.Process#getArguments
     * @param   string cmd
     * @return  string[] arguments
     */
    public static function parseCommandLine($cmd) {

      // Remove executable from command line. If it's quoted, handle this
      // accordingly (with either single and double quotes). If the command
      // line exists entirely of the command, return an empty array
      if ('"' === $cmd{0}) {
        $cmd= substr($cmd, strpos($cmd, '"', 1)+ 2);
      } else if ("'" === $cmd{0}) {
        $cmd= substr($cmd, strpos($cmd, "'", 1)+ 2);
      } else if (FALSE !== ($end= strpos($cmd, ' '))) {
        $cmd= substr($cmd, $end+ 1);
      } else {
        return array();
      }

      // Parse arguments. These also may be quoted (again, either with " or '), 
      // or even partially quoted, so handle this, too.
      $arguments= array();
      $o= 0;
      while (FALSE !== ($p= strcspn($cmd, ' ', $o))) {
        $option= substr($cmd, $o, $p);
        if (1 === substr_count($option, '"')) {
          $l= $o+ $p;
          $qp= strpos($cmd, '"', $l)+ 1;
          $option.= substr($cmd, $l, $qp- $l);
          $o= $qp+ 1;
        } else if (1 === substr_count($option, "'")) {
          $l= $o+ $p;
          $qp= strpos($cmd, "'", $l)+ 1;
          $option.= substr($cmd, $l, $qp- $l);
          $o= $qp+ 1;
        } else {
          $o+= $p+ 1;
        }
        $arguments[]= $option;
      }
      return $arguments;
    }
    
    /**
     * Get command line arguments
     *
     * @return  string[]
     */
    public function getArguments() {
      if (NULL === $this->status['arguments']) {
        $this->status['arguments']= self::parseCommandLine($this->status['command']);
      }
      return $this->status['arguments'];
    }
    
    /**
     * Get error stream
     *
     * @return  io.File STDERR
     */
    public function getErrorStream() {
      return $this->err;
    }

    /**
     * Get input stream
     *
     * @return  io.File STDIN
     */
    public function getInputStream() {
      return $this->in;
    }
    
    /**
     * Get output stream
     *
     * @return  io.File STDOUT
     */
    public function getOutputStream() {
      return $this->out;
    }
    
    /**
     * Returns the exit value for the process
     *
     * @return  int
     */
    public function exitValue() {
      return $this->exitv;
    }
    
    /**
     * Close this process
     *
     * @return  int exit value of process
     */
    public function close() {
      $this->in->isOpen() && $this->in->close();
      $this->out->isOpen() && $this->out->close();
      $this->err->isOpen() && $this->err->close();
      $this->exitv= proc_close($this->_proc);
      return $this->exitv;
    }
  }
?>
