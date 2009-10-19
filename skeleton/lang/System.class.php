<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('lang.SystemException');
  
  // Known return codes
  define('SYSTEM_RETURN_CMDNOTFOUND',        127);
  define('SYSTEM_RETURN_CMDNOTEXECUTABLE',   126);
  
  /**
   * The System class contains several useful class fields and methods. 
   * It cannot be instantiated.
   * 
   */
  class System extends Object {
  
    /**
     * Private helper method. Tries to locate an environment
     * variable by name, and, if it fails, tries the given
     * alternatives in the sequence they are specified
     *
     * @param   string* args
     * @return  string environment variable by name
     */
    protected static function _env() {
      foreach (func_get_args() as $a) {
        if ($e= getenv($a)) return $e;
      }
      return $e;
    }

    /**
     * Retrieve system property. Note: Results of this method are
     * cached!
     *
     * Known property names:
     * <pre>
     * php.version       PHP version
     * php.api           PHP api
     * os.name           Operating system name
     * os.tempdir        System-wide temporary directory
     * host.name         Host name
     * host.arch         Host architecture
     * user.home         Current user's home directory
     * user.name         Current user's name
     * file.separator    File separator ("/" on UNIX)
     * path.separator    Path separator (":" on UNIX)
     * </pre>
     *
     * @param   string name
     * @return  mixed
     */
    public static function getProperty($name) {
      static $prop= array();
      
      if (!isset($prop[$name])) switch ($name) {
        case 'php.version': 
          $prop[$name]= PHP_VERSION; 
          break;
          
        case 'php.api': 
          $prop[$name]= PHP_SAPI;
          break;

        case 'os.name': 
          $prop[$name]= PHP_OS; 
          break;

        case 'os.tempdir':
          $prop[$name]= self::tempDir();
          break;
        
        case 'host.name': 
          if (extension_loaded('posix')) {
            $uname= posix_uname();
            $prop[$name]= $uname['nodename'].(isset ($uname['domainname'])
              ? '.'.$uname['domainname']
              : ''
            );
            break;
          }
          $prop[$name]= self::_env('HOSTNAME', 'COMPUTERNAME');
          break;

        case 'host.arch':
          if (extension_loaded('posix')) {
            $uname= posix_uname();
            $prop[$name]= $uname['machine'];
            break;
          }
          $prop[$name]= self::_env('HOSTTYPE', 'PROCESSOR_ARCHITECTURE');
          break;
          
        case 'user.home': 
          if (extension_loaded('posix')) {
            $pwuid= posix_getpwuid(posix_getuid());
            $prop[$name]= $pwuid['dir'];
            break;
          }
          $prop[$name]= str_replace('\\', DIRECTORY_SEPARATOR, self::_env('HOME', 'HOMEPATH'));
          break;
          
        case 'user.name': 
          $prop[$name]= get_current_user();
          break;
        
        case 'file.separator':
          return DIRECTORY_SEPARATOR;
        
        case 'path.separator':
          return PATH_SEPARATOR;
      }
      
      return $prop[$name];
    }
    
    /**
     * Sets an environment variable
     *
     * @param   string name
     * @param   mixed var
     * @return  bool success
     */
    public static function putEnv($name, $var) {
      return putenv($name.'='.$var);
    }
    
    /**
     * Returns the contents of an environment variable, or in case it does
     * not exist, FALSE.
     *
     * @param   string name
     * @return  mixed var
     */
    public static function getEnv($name) {
      return getenv($name);
    }
    
    /**
     * Retrieve location of temporary directory. This method looks at the 
     * environment variables TEMP and TMP, and, if these cannot be found,
     * uses '/tmp' as location on Un*x systems, c:\ on Windows.
     * 
     * @see     php://tempnam
     * @return  string
     */
    public static function tempDir() {
      if (getenv('TEMP')) {
        $dir= getenv('TEMP');
      } else if (getenv('TMP')) {
        $dir= getenv('TMP');
      } else {
        $dir= (0 == strcasecmp(substr(PHP_OS, 0, 3), 'WIN')) ? 'c:\\' : '/tmp';
      }

      return rtrim($dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
    }

    /** 
     * Execute an external program
     * 
     * Copied from man bash, some information on the exit code
     * <pre>
     * EXIT STATUS
     * For  the  shell's  purposes,  a command which exits with a
     * zero exit status has succeeded.  An exit  status  of  zero
     * indicates success.  A non-zero exit status indicates fail­
     * ure.  When a command terminates on a fatal signal N,  bash
     * uses the value of 128+N as the exit status.
     *
     * If  a  command  is not found, the child process created to
     * execute it returns a status of 127.  If a command is found
     * but is not executable, the return status is 126.
     *
     * If a command fails because of an error during expansion or
     * redirection, the exit status is greater than zero.
     *
     * Shell builtin commands return a status of 0 (true) if suc­
     * cessful,  and  non-zero  (false)  if an error occurs while
     * they execute.  All builtins return an exit status of 2  to
     * indicate incorrect usage.
     *
     * Bash  itself  returns  the exit status of the last command
     * executed, unless a syntax error occurs, in which  case  it
     * exits  with  a  non-zero value.  See also the exit builtin
     * command below.
     * </pre>
     *
     * @param   string cmdLine the command
     * @param   string redirect default '2>&1' redirection
     * @param   bool background
     * @return  array lines
     * @throws  lang.SystemException in case the return code is not zero
     * @see     php://exec
     * @see     xp://lang.Process
     */
    public static function exec($cmdLine, $redirect= '2>&1', $background= FALSE) {
      $cmdLine= escapeshellcmd($cmdLine).' '.$redirect.($background ? ' &' : '');
      
      if (!($pd= popen($cmdLine, 'r'))) throw new XPException(
        'cannot execute "'.$cmdLine.'"'
      );
      $buf= array();
      while (
        (!feof($pd)) && 
        (FALSE !== ($line= fgets($pd, 4096)))
      ) {
        $buf[]= chop($line);
      }
      $retCode= (pclose($pd) >> 8) & 0xFF;
      
      if ($retCode != 0) throw new SystemException(
        'System.exec('.$cmdLine.') err #'.$retCode.' ['.implode('', $buf).']',
        $retCode
      );
      return $buf;
    }
  }  
?>
