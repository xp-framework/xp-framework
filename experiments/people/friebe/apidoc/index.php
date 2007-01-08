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
      $basedir= dirname(__FILE__).DIRECTORY_SEPARATOR;
      $builddir= $basedir.'build'.DIRECTORY_SEPARATOR;
      
      if (0 != sscanf($request->getQueryString(), '%[^:]:%[^<[]', $type, $arg) && $arg) {
        if (!file_exists($xml= $builddir.basename($arg).'.xml')) {
          return throw(new HttpScriptletException(htmlspecialchars($arg).' does not exist!', HTTP_NOT_FOUND));
        }
        
        $proc= &new DomXSLProcessor();
        $proc->setXMLFile($xml);
        $proc->setXSLFile($basedir.$type.'.xsl');
        $proc->run();
        $response->write($proc->output());
        return;
      }

      $response->write('<html><head><link rel="stylesheet" href="style.css"/></head><body>');
      $response->write('<h1>'.basename($builddir).'</h1>');
      $c= &new FileCollection($builddir);
      $c->open();
      $response->write('<dir>');
      while (NULL !== ($element= &$c->next())) {
        $package= basename(basename($element->getURI()), '.xml');
        
        // HACK
        $indicator= substr($package, strrpos($package, '.')+ 1, 1);
        if (strtolower($indicator) != $indicator) continue;
        
        $response->write('<li><a href="?package:'.$package.'">'.$package.'</a></li>');
      }
      $response->write('</dir>');
      $c->close();
    }
  }


  scriptlet::run(new DocumentationScriptlet());
?>
