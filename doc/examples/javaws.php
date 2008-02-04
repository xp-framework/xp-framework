<?php
/* This file is part of the XP framework's examples
 *
 * $Id$
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'com.sun.webstart.jnlp.JnlpDocument', 
    'peer.http.HttpConnection',
    'lang.System',
    'lang.Process',
    'io.File',
    'io.FileUtil',
    'peer.URL',
    'io.Folder'
  );
  
  // {{{ string makeLink(peer.URL codebase, string href)
  //     Returns fully qualified link for a given codebase and a specified URI reference
  function makeLink($codebase, $href) {
    if (strstr($href, '://')) return $href;   // Fully qualified
    
    $base= sprintf(
      '%s://%s:%d',
      $codebase->getScheme(),
      $codebase->getHost(),
      $codebase->getPort()
    );

    if ('/' == $href{0}) return $base.$href;  // Absolute

    return $base.rtrim($codebase->getPath(), '/').'/'.$href;  // Relative
  }
  // }}}
  
  // {{{ main
  $p= new ParamString();
  if (!$p->exists(1)) {
    Console::writeLinef('Usage: %s <url_to_jnlp_file> [--java=<java_executable>]', $p->value(0));
    exit(1);
  }
  
  $uri= $p->value(1);
  if (strstr($uri, '://')) {
    Console::writeLine('===> Downloading webstart URL ', $uri);
    try {
      $c= new HttpConnection($uri);
      if ($response= $c->get()) {
        $document= '';
        while (FALSE !== ($buf= $response->readData())) {
          $document.= preg_replace('/&(?!(amp;))/', '&amp;', $buf);
        }
      }
      delete($c);
    } catch (XPException $e) {
      $e->printStackTrace();
      exit(-1);
    }
  } else {
    Console::writeLine('===> Reading webstart document ', $uri);
    try {
      $document= preg_replace('/&(?!(amp;))/', '&amp;', FileUtil::getContents(new File($uri)));
    } catch (XPException $e) {
      $e->printStackTrace();
      exit(-1);
    }
  }
  
  try {
    $j= JnlpDocument::fromString($document);
  } catch (XPException $e) {
    $e->printStackTrace();
    exit(-1);
  }
  
  // Print out information provided by the JNLP information section
  $inf= $j->getInformation();
  Console::writeLinef(
    "---> Application is %s (%s)\n     Vendor %s (see %s)", 
    $inf->getTitle(),
    $inf->getDescription(JNLP_DESCR_SHORT),
    $inf->getVendor(),
    $inf->getHomepage()
  );
  
  // Create an application directory
  $folder= new Folder(strtr(basename($j->getCodebase()), PATH_SEPARATOR, '_'));
  try {
    if (!$folder->exists()) $folder->create();
  } catch (XPException $e) {
    $e->printStackTrace();
    exit(-1);
  }
  
  // Download all JAR files, adding them to the classpath as we go
  $classpath= System::getEnv('CLASSPATH');
  $properties= '';
  $codebase= new URL($j->getCodebase());
  Console::writeLinef('---> Processing resources from codebase %s', $j->getCodebase());
  foreach ($j->getResources() as $resource) {
    switch (xp::typeOf($resource)) {

      // A JAR file, download it
      case 'com.sun.webstart.jnlp.JnlpJarResource':
        $href= new URL(makeLink($codebase, $resource->getHref()));
        
        $target= rtrim($folder->getURI(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $href->getPath());
        $classpath.= PATH_SEPARATOR.'"'.$target.'"';
        try {
          $params= array();

          // Create a new file instance
          $jar= new File($target);
          if ($jar->exists()) {
            Console::writef('     >> Have %s... ', basename($jar->getURI()));
            $params['If-Modified-Since']= date('D, d M Y H:i:s \G\M\T', $jar->lastModified());
          }


          // Issue HTTP request
          $url= new URL(makeLink($codebase, $resource->getLocation()));
          $c= new HttpConnection($url);
          $response= $c->get($url->getParams(), $params);
          Console::write('     << ', $response->getStatuscode(), ' "', $response->getMessage(), '": ');
          
          // Check response code
          switch ($response->getStatusCode()) {
            case 200:
              Console::writeLine();
              Console::writef('     >> Downloading %s... ', $href->getURL());

              // Check if this file resided in a subdirectory. If so, create this
              // subdirectory if necessary
              $f= new Folder(dirname($jar->getURI()));
              if (!$f->exists()) $f->create();

              $jar->open(FILE_MODE_WRITE);
              while (FALSE !== ($buf= $response->readData(0x2000, $binary= TRUE))) {
                $jar->write($buf);
              }
              $jar->close();
              Console::writeLine('OK');
              break;
              
            case 304:
              Console::writeLine('(cached)');
              break;
            
            case 404:
              Console::writeLinef('FAIL %s... ', $url);
              break;

            default:
              Console::writeLine('FAIL');
          }
        } catch (XPException $e) {
          Console::writeLine('FAIL');
          $e->printStackTrace();
          exit(-1);
        }
        break;

      // A property, add it to the Java command line
      case 'com.sun.webstart.jnlp.JnlpPropertyResource':
        Console::writeLine('     >> Have property ', $resource->getName(), '=', $resource->getValue());
        $properties.= ' -D'.$resource->getName().'=\''.$resource->getValue().'\'';
        break;
      
      // Ignore anything else
      default:
        Console::writeLine('     >> Ignoring ['.$resource->getClassName().']');
    }
  }
  
  // 
  
  // Execute Java
  $app= $j->getApplicationDesc();
  $cmd= $p->value('java', 'j', 'java');
  $params= array(
    $properties,
    '-cp '.$classpath,
    $app->getMain_Class(),
    implode(' ', $app->getArguments()),
    '2>&1'
  );
  Console::writeLine(sprintf('---> Executing %s %s', $cmd, implode(' ', $params)));

  try {
    $proc= new Process($cmd, $params);
    while (!$proc->out->eof()) {
      Console::writeLine($proc->out->readLine());
    }
  } catch (XPException $e) {
    $e->printStackTrace();
    exit(-1);
  }
  
  // Pass exit value to caller
  exit($proc->close());
  // }}}
?>
