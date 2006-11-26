<?php
/* This class is part of the XP framework
 *
 * $Id: SandboxSourceRunner.class.php 8384 2006-11-10 12:47:17Z kiesel $ 
 */

  uses('lang.Process');

  /**
   * Sandbox source code runner
   *
   * @see      xp://lang.Process
   * @purpose  Runs sourcecode in a sandbox
   */
  class SandboxSourceRunner extends Object {
    public
      $executable   = '',
      $settings     = array(),
      $source       = '',
      $stdout       = array(),
      $stderr       = array(),
      $exitcode     = '';

    /**
     * Constructor
     *
     * @access  public
     * @throws  lang.IllegalStateException if sapi does not support forking
     */
    public function __construct() {
      if (!isset($_SERVER['_'])) {
        throw(new IllegalStateException('Sandbox not supported in sapi '.php_sapi_name()));
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
    public function setExecutable($executable) {
      $this->executable= $executable;
    }

    /**
     * Get Executable
     *
     * @access  public
     * @return  string
     */
    public function getExecutable() {
      return $this->executable;
    }

    /**
     * Set Settings
     *
     * @access  public
     * @param   string key
     * @param   string value
     */
    public function setSetting($key, $value) {
      $this->settings[$key]= $value;
    }
    
    /**
     * Retrieve a single setting
     *
     * @access  public
     * @param   string key
     * @return  string
     */
    public function getSetting($key) {
      return $this->settings[$key];
    }    

    /**
     * Get Settings
     *
     * @access  public
     * @return  mixed[]
     */
    public function getSettings() {
      return $this->settings;
    }

    /**
     * Set Source
     *
     * @access  public
     * @param   string source
     */
    public function setSource($source) {
      $this->source= $source;
    }

    /**
     * Get Source
     *
     * @access  public
     * @return  string
     */
    public function getSource() {
      return $this->source;
    }

    /**
     * Set Stdout
     *
     * @access  public
     * @param   string stdout
     */
    public function setStdout($stdout) {
      $this->stdout= $stdout;
    }

    /**
     * Get Stdout
     *
     * @access  public
     * @return  string
     */
    public function getStdout() {
      return $this->stdout;
    }

    /**
     * Set Stderr
     *
     * @access  public
     * @param   string stderr
     */
    public function setStderr($stderr) {
      $this->stderr= $stderr;
    }

    /**
     * Get Stderr
     *
     * @access  public
     * @return  string
     */
    public function getStderr() {
      return $this->stderr;
    }

    /**
     * Set Exitcode
     *
     * @access  public
     * @param   string exitcode
     */
    public function setExitcode($exitcode) {
      $this->exitcode= $exitcode;
    }

    /**
     * Get Exitcode
     *
     * @access  public
     * @return  string
     */
    public function getExitcode() {
      return $this->exitcode;
    }

    /**
     * Run the sourcecode in a sandbox
     *
     * @access  public
     * @param   string source
     * @return  int exitcode
     */
    public function run($source) {
      try {
        $cmdline= $this->getExecutable();
        foreach ($this->settings as $key => $value) {
          $cmdline.= sprintf(' -d%s=%s', $key, $value);
        }
        
        $p= new Process($cmdline);
        $p->in->write('<?php '.$source.'?>');
        $p->in->close();
        
        while (!$p->out->eof()) {
          $l= trim($p->out->readLine());
          if (!empty($l)) $this->stdout[]= $l;
        }
        
        while (!$p->err->eof()) {
          $l= trim($p->err->readLine());
          if (!empty($l)) $this->stderr[]= $l;
        }

        $p->close();
      } catch (Exception $e) {
        throw($e);
      }
      
      return $this->exitcode= $p->exitValue();
    }
  }
?>
