<?php
  require('lang.base.php');
  xp::sapi('scriptlet.production');
  uses(
    'scriptlet.HttpScriptlet',
    'io.collections.FileCollection',
    'xml.DomXSLProcessor'
  );
  
  class DocumentationScriptlet extends HttpScriptlet {
  
    function doGet(&$request, &$response) {
      $this->basedir= dirname(__FILE__).DIRECTORY_SEPARATOR;
      $this->builddir= $this->basedir.'build'.DIRECTORY_SEPARATOR;

      if ($class= $request->getQueryString()) {
        
        $proc= &new DomXSLProcessor();
        $proc->setXMLFile($this->builddir.basename($class).'.xml');
        $proc->setXSLFile($this->basedir.'apidoc.xsl');
        $proc->run();
        $response->write($proc->output());
        return;
      }

      $response->write('<h1>'.basename($this->builddir).'</h1>');
      $c= &new FileCollection($this->builddir);
      $c->open();
      $response->write('<dir>');
      while (NULL !== ($element= &$c->next())) {
        $class= basename($element->getURI());
        $response->write('<li><a href="?'.basename($class, '.xml').'">'.$class.'</a></li>');
      }
      $response->write('</dir>');
      $c->close();
    }
  }


  scriptlet::run(new DocumentationScriptlet());
?>
