<?php
/* This class is part of the XP framework
 *
 * $Id$
 *
 */

  uses('xml.XML');
  
  /**
   * XSL-Prozessor, aufbauend auf den PHP-XSL-Funktionen (Sablotron)
   * Beispiel:
   * <code>
   * uses('xml.XSLProcessor');
   *
   * $proc= new XSLProcessor();
   * $proc->setXSLFile('test.xsl');
   * $proc->setXMLFile('test.xml');
   * try {
   *   $proc->run();
   * } catch($e) {
   *   var_dump($e);
   *   exit;
   * }
   * echo $proc->output();
   * </code>
   *
   * @see http://www.gingerall.com
   */
  class XSLProcessor extends XML {
    var 
      $processor,
      $stylesheet,
      $buffer,
      $params,
      $output;

    /**
     * Constructor
     */
    function __construct($params= NULL) {
      Object::__construct($params);
      $this->processor= xslt_create();
      $this->params= array();
    }
    
    /**
     * Den Error-Handler setzen
     *
     * @param   (string)funcName Die Callback-Funktion
     */
    function setErrorHandler($funcName) {
      xslt_set_error_handler($this->processor, $funcName);
    }

    /**
     * Den Scheme-Handler setzen
     *
     * @param   array defines
     */
    function setSchemeHandler($defines) {
      xslt_set_scheme_handlers($this->processor, $defines);
    }

    /**
     * Das Base-Dir setzen
     *
     * @param   string dir
     */
    function setBase($dir, $proto= 'file://') {
      if ('/' != $dir[strlen($dir)- 1]) $dir.= '/';
      xslt_set_base($this->processor, $proto.$dir);
    }

    /**
     * Eine Datei als XSL-Input definieren
     *
     * @param   (string)file Dateiname
     */
    function setXSLFile($file) {
      $this->stylesheet= array($file, NULL);
    }
    
    /**
     * Einen String als XSL-Input definieren
     *
     * @param   (string)xsl Das XSL-Dokument als String
     */
    function setXSLBuf($xsl) {
      $this->stylesheet= array('arg:/_xsl', $xsl);
    }

    /**
     * Eine Datei als XML-Input definieren
     *
     * @param   (string)file Dateiname
     */
    function setXMLFile($file) {
      $this->buffer= array($file, NULL);
    }
    
    /**
     * Einen String als XML-Input definieren
     *
     * @param   (string)xml Das XML-Dokument als String
     */
    function setXMLBuf($xml) {
      $this->buffer= array('arg:/_xml', $xml);
    }

    /**
     * Alle Parameter auf einmal definieren
     *
     * @param   (array)params Assoziativer Array aus {paramname} => {paramvalue}
     */
    function setParams($params) {
      $this->params= $params;
    }
    
    /**
     * Einzelnen Parameter definieren
     *
     * @param   (string)name Name des Parameters
     * @param   (string)val  Wert des Parameters
     */
    function setParam($name, $val) {
      $this->params[$name]= $val;
    }

    /**
     * Die Transformation staren
     *
     * @return  (bool)success Ob die Transformation geklappt hat
     */
    function run($buffers= array()) {
      if (NULL != $this->buffer[1]) $buffers['/_xml']= &$this->buffer[1];
      if (NULL != $this->stylesheet[1]) $buffers['/_xsl']= &$this->stylesheet[1];
      
      // 4.1.1 API?
      if (!function_exists('xslt_fetch_result')) {
        //var_dump($buffers);
        $this->output= xslt_process(
          $this->processor, 
          $this->buffer[0],
          $this->stylesheet[0],
          NULL,
          $buffers,
          $this->params
        );
        return ($this->output !== FALSE);
      }

      // 4.0.6 API
      $result= xslt_run(
        $this->processor,
	$this->stylesheet[0],
	$this->buffer[0],
	'arg:/_out',
        $this->params,
	$buffers
      );
      $this->output= xslt_fetch_result($this->processor, '/_out');
      
      return $result; // ($this->output !== FALSE);
    }

    /**
     * Den Output der Transformation "abholen"
     *
     * @return   (string)output Das Ergebnis der Transformation
     */
    function output() {
      return $this->output;
    }

    /**
     * Destructor
     */
    function __destruct() {
      xslt_free($this->processor);
      Object::__destruct();
    }
  }
?>
