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
      $response->write('<html><head><link rel="stylesheet" href="style.css"/></head><body>');
      
      $basedir= dirname(__FILE__).DIRECTORY_SEPARATOR;
      $builddir= $basedir.'build'.DIRECTORY_SEPARATOR;

      if (0 != sscanf($request->getQueryString(), '%[^<[]', $class) && $class) {
        if (!file_exists($xml= $builddir.basename($class).'.xml')) {
          return throw(new HttpScriptletException(htmlspecialchars($class).' does not exist!', HTTP_NOT_FOUND));
        }
        
        $proc= &new DomXSLProcessor();
        $proc->setXMLFile($xml);
        $proc->setXSLFile($basedir.'apidoc.xsl');
        $proc->run();
        $response->write($proc->output());
        return;
      }

      $response->write('<h1>'.basename($builddir).'</h1>');
      $c= &new FileCollection($builddir);
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
