<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.Process');

  /**
   * Sandbox source code runner
   *
   * @see      xp://lang.Process
   * @purpose  Runs sourcecode in a sandbox
   */
  class SandboxSourceRunner extends Object {
    var
      $executable   = '',
      $settings     = array(),
      $source       = '',
      $stdout       = '',
      $stderr       = '',
      $exitcode     = '';

    /**
     * Constructor
     *
     * @access  public
     * @throws  lang.IllegalStateException if sapi does not support forking
     */
    function __construct() {
      if (!isset($_SERVER['_'])) {
        return throw(new IllegalStateException('Sandbox not supported in sapi '.php_sapi_name()));
      }
      
      $this->setExecutable(preg_replace('#^/cygdrive/(\w)/#', '$1:/', $_SERVER['_']));
      $this->setSetting('include_path', ini_get('include_path'));
    }

    /**
     * Set Executable
     *
     * @access  public
     * @param   string executable
     */
    function setExecutable($executable) {
      $this->executable= $executable;
    }

    /**
     * Get Executable
     *
     * @access  public
     * @return  string
     */
    function getExecutable() {
      return $this->executable;
    }

    /**
     * Set Settings
     *
     * @access  public
     * @param   string key
     * @param   string value
     */
    function setSetting($key, $value) {
      $this->settings[$key]= $value;
    }

    /**
     * Get Settings
     *
     * @access  public
     * @return  mixed[]
     */
    function getSettings() {
      return $this->settings;
    }

    /**
     * Set Source
     *
     * @access  public
     * @param   string source
     */
    function setSource($source) {
      $this->source= $source;
    }

    /**
     * Get Source
     *
     * @access  public
     * @return  string
     */
    function getSource() {
      return $this->source;
    }

    /**
     * Set Stdout
     *
     * @access  public
     * @param   string stdout
     */
    function setStdout($stdout) {
      $this->stdout= $stdout;
    }

    /**
     * Get Stdout
     *
     * @access  public
     * @return  string
     */
    function getStdout() {
      return $this->stdout;
    }

    /**
     * Set Stderr
     *
     * @access  public
     * @param   string stderr
     */
    function setStderr($stderr) {
      $this->stderr= $stderr;
    }

    /**
     * Get Stderr
     *
     * @access  public
     * @return  string
     */
    function getStderr() {
      return $this->stderr;
    }

    /**
     * Set Exitcode
     *
     * @access  public
     * @param   string exitcode
     */
    function setExitcode($exitcode) {
      $this->exitcode= $exitcode;
    }

    /**
     * Get Exitcode
     *
     * @access  public
     * @return  string
     */
    function getExitcode() {
      return $this->exitcode;
    }

    /**
     * Run the sourcecode in a sandbox
     *
     * @access  public
     * @param   string source
     * @return  int exitcode
     */
    function run($source) {
      try(); {
        $cmdline= $this->getExecutable();
        foreach ($this->settings as $key => $value) {
          $cmdline.= sprintf(' -d%s=%s', $key, $value);
        }
        
        $p= &new Process($cmdline);
        $p->in->write('<?php '.$source.'?>');
        $p->in->close();
        
        while ($l= $p->out->readLine()) {
          $this->stdout[]= $l;
        }
        
        while ($l= $p->err->readLine()) {
          $this->stderr[]= $l;
        }

        $p->close();
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      
      return $this->exitcode= $p->exitValue();
    }
  }
?>
