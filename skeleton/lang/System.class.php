<?php
/* 
 * $Id$
 *
 * Diese Klasse ist Bestandteil des XP-Frameworks
 * (c) 2001 Timm Friebe, Schlund+Partner AG
 * 
 * @see http://doku.elite.schlund.de/projekte/xp/skeleton/
 * 
 */

  uses('lang.SystemException');
  
  // Known return codes
  define('SYSTEM_RETURN_CMDNOTFOUND',        127);
  define('SYSTEM_RETURN_CMDNOTEXECUTABLE',   126);
  
  /**
   * Die System-Klasse
   * Betriebssystem/Umgebungsspezifisches
   * 
   * @access  static
   */
  class System extends Object {

    /**
     * System-Infos
     *
     * @access  public
     * @return  array Properties
     */
    function getInfo() {
      $prop= array();
      
      // PHP-spezifisches
      $prop['php.version']= PHP_VERSION;
      $prop['php.api']= PHP_SAPI;
      $prop['os.name']= PHP_OS;

      // Host
      $prop['host.name']= (getenv('HOSTNAME')== '') ? getenv('COMPUTERNAME') : getenv('HOSTNAME');
      $prop['host.arch']= (getenv('HOSTTYPE')== '') ? getenv('PROCESSOR_ARCHITECTURE') : getenv('HOSTTYPE');

      // User
      $prop['user.home']= str_replace('\\', '/', (getenv('HOME')== '') ? getenv('HOMEPATH') : getenv('HOME'));
      $prop['user.name']= (getenv('USER')== '') ? getenv('USERNAME') : getenv('USER');
      
      return $prop;
    }
    
    /**
     * Setzt eine Umgebungsvariable
     *
     * @access  public
     * @param   string name
     * @param   mixed var
     * @return  bool success
     */
    function putEnv($name, $var) {
      return putenv($name.'='.$var);
    }
    
    /**
     * Liest eine Umgebungsvariable. Gibt es diese nicht, wird NULL zurückgegeben
     *
     * @access  public
     * @param   string name
     * @return  mixed var
     */
    function getEnv($name) {
      $val= getenv($name);
      return ('' == $val) ? NULL : $val;
    }

    /** 
     * Ein Programm ausführen
     * 
     * Zum Exit-Code, aus der Bash-Manpage:
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
     * @param	string cmdLine  Der auszuführende Befehl
     * @param   string redirect Ausgabe-Umleitung. Per Default wird STDERR auf STDOUT gemappt.
     * @param   bool background Im Hintergrund ausführen?
     * @return  array lines Die einzelnen Zeilen der Rückgabe als Array
     * @throws  Exception wenn Retcode != 0
     * @see     http://www.php.net/manual/en/function.exec.php#contrib
     */
    function exec($cmdLine, $redirect= '2>&1', $background= FALSE) {
      $cmdLine= escapeshellcmd($cmdLine).' '.$redirect.($background ? ' &' : '');
      
      if (!($pd= popen($cmdLine, 'r'))) return throw(new Exception(
        'cannot execute "'.$cmdLine.'"'
      ));
      $buf= array();
      while (
        (!feof($pd)) && 
        (FALSE !== ($line= fgets($pd, 4096)))
      ) {
        $buf[]= $line;
      }
      $retCode= (pclose($pd) >> 8) & 0xFF;
      
      if ($retCode != 0) throw(new SystemException(
        'System.exec('.$cmdLine.') err #'.$retCode.' ['.implode('', $buf).']',
        $retCode
      ));
      return $buf;
    }
  }  
?>
