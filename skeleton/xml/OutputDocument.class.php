<?php
  /* Output-Handling
   *
   * $Id$
   */
   
  import('xml.Tree');
  import('xml.XSLProcessor');
  
  class OutputDocument extends Tree {
    var 
      $title,
      $language,
      $product;
    
    var $buffer= '';
    var $stylesheet= NULL;
    var $params= NULL;
    
    // Output-Buffering aktiv?
    var $_ob;
    
    function OutputDocument($params= NULL) {
      $this->__construct($params);
    }
    
    function __construct($params= NULL) {
      parent::__construct($params);
      $this->language= 'default';
      $this->product= 'default';
      
      $this->processor= new XSLProcessor();
      $this->processor->setErrorHandler('OutputDocument__XSL_error');

      ob_start();
      $this->_ob= TRUE;
    }
    
    function render($echo= TRUE) {
      if (!$this->_ob) return;
      $this->buffer= 
        "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n".
        $this->getSource(FALSE);

      logline_text("buffer", $this->buffer);
      $this->processor->setXSLFile($this->stylesheet);
      $this->processor->setXMLBuf(&$this->buffer);
      $this->processor->setParams(&$this->params);
      
      if (!$this->processor->run()) {
        logline_text("buffer", $this->buffer);
        return FALSE;
      }
      
      ob_end_clean();
      if ($echo) echo $this->processor->output();
      flush();
      $this->_ob= FALSE;
    }
    
    function redirect($target) {
      LOG::info($GLOBALS['REQUEST_URI'].' ==> redirect ['.$target.']');
      if (!headers_sent()) {
        header('HTTP/1.1 302 Moved');
        header('Location: '.$target);
        ob_end_clean();
        $this->_ob= FALSE;
        return TRUE;
      }
      LOG::warn('redirect::headers already sent');
      return FALSE;
    }
    
    function __destruct() {
      if ($this->_ob) {
        LOG::info('render ['.$this->_ob.']');
        $this->render();
      }
      if (is_resource($this->processor)) {
        $this->processor->__destruct();
      }
      parent::__destruct();
    }
  }
  
  function OutputDocument__XSL_error($parser, $code, $level, $error) {
    if (
      (3 != $code) &&   // XSL-Error
      (4 != $code)      // Not found
    ) return;

    LOG::warn('xsl #'.$code.'@'.$level.' => "'.$error['code'].'"');
    LOG::warn($error);

    // Status
    $codeMap= array(
      0x0004 => 404,          // [SABLOT] Cannot open File 'bla.xsl'
      0x0F00 => 501           // [E_USER] Session
    );
    $statusCode= isset($codeMap[$error['code']]) ? $codeMap[$error['code']] : 500;
    header('HTTP/1.1 '.$statusCode.' Error');
    ob_end_clean();

    // Fehlerausgabe
    $errorFile= getenv('DOCUMENT_ROOT').'/../static/'.getenv('LANG').'/error'.$statusCode.'.html';
    if (!file_exists($errorFile)) {
      $errorFile= getenv('DOCUMENT_ROOT').'/../static/error405.html';
    }
    @readfile($errorFile);

    /* Sablotron & Debian:: ARGL! Das $error-Array ist total korrumpiert
    $errorDocument= @implode('', @file(
      $DOCUMENT_ROOT.'/../static/'.getenv('LANG').'/error'.$statusCode.'.html'
    ));
    if (empty($errorDocument)) $errorDocument= '<xp:value-of select="reason"/>';
    echo str_replace(
      '<xp:value-of select="reason"/>', 
      sprintf(
        "%s #%d at line %d in <b>%s</b>\n%s\n",
        $error['msgtype'],
        $code,
        @$error['line'],
        preg_replace('|(file:)?'.dirname($DOCUMENT_ROOT).'|', '', @$error['URI']),
        preg_replace('|(file:)?'.dirname($DOCUMENT_ROOT).'|', '', $error['msg'])
      ),
      $errorDocument
    );
    */
    return TRUE;
  }
?>
