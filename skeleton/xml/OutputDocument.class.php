<?php
  /* This class is part of the XP framework
   *
   * $Id$
   */
   
  uses(
    'xml.Tree',
    'xml.XSLProcessor'
  );
  
  /**
   * @purpose Output-Handling
   *
   */
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
    
    function __construct($params= NULL) {
      parent::__construct($params);
      $this->language= 'default';
      $this->product= 'default';
      
      $this->processor= &new XSLProcessor();
      $this->processor->setErrorHandler(array($this, '_message'));

      ob_start();
      $this->_ob= TRUE;
    }
    
    function render($echo= TRUE) {
      if (!$this->_ob) return;
      $this->buffer= 
        "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n".
        $this->getSource(FALSE);

      // logline_text("buffer", $this->buffer);
      $this->processor->setXSLFile($this->stylesheet);
      $this->processor->setXMLBuf(&$this->buffer);
      $this->processor->setParams(&$this->params);
      
      if (!$this->processor->run()) {
        // logline_text("buffer", $this->buffer);
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
        @ob_end_clean();
        $this->_ob= FALSE;
        return TRUE;
      }
      LOG::warn('redirect::headers already sent');
      return FALSE;
    }
    
    function _message($parser, $code, $level, $error) {
      LOG::warn('xsl error code#'.$code.', level '.$level);
      LOG::warn(array_values($error));
      list($msgtype, $code, $module, $msg)= array_values($error);
      
      // Only erors, no log or warnings
      if ('error' != $msgtype) return;
      
      // Status
      $codeMap= array(
        0x0004 => 404,          // [SABLOT] Cannot open File 'bla.xsl'
        0x0F00 => 501           // [E_USER] Session
      );
      $statusCode= isset($codeMap[$code]) ? $codeMap[$code] : 500;
      header('HTTP/1.1 '.$statusCode.' Error');
      ob_end_clean();

      // Fehlerausgabe
      $errorFile= getenv('DOCUMENT_ROOT').'/../static/'.getenv('LANG').'/error'.$statusCode.'.html';
      if (!file_exists($errorFile)) {
        $errorFile= getenv('DOCUMENT_ROOT').'/../static/error405.html';
      }
      @readfile($errorFile);
      
      return TRUE;
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
?>
