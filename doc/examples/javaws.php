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
    'peer.URL',
    'io.Folder'
  );
  
  // {{{ main
  $p= &new ParamString();
  if (!$p->exists(1)) {
    Console::writeLinef('Usage: %s <url_to_jnlp_file> [--java=<java_executable>]', $p->value(0));
    exit(1);
  }
  
  Console::writeLine('===> Downloading webstart URL ', $p->value(1));
  try(); {
    $c= &new HttpConnection($p->value(1));
    if ($response= &$c->get()) {
      $document= '';
      while (FALSE !== ($buf= $response->readData())) {
        $document.= preg_replace('/&(?!(amp;))/', '&amp;', $buf);
      }
    }
    delete($c);
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  try(); {
    $j= &JnlpDocument::fromString($document);
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  // Print out information provided by the JNLP information section
  $inf= &$j->getInformation();
  Console::writeLinef(
    "---> Application is %s (%s)\n     Vendor %s (see %s)", 
    $inf->getTitle(),
    $inf->getDescription(JNLP_DESCR_SHORT),
    $inf->getVendor(),
    $inf->getHomepage()
  );
  
  // Create an application directory
  $folder= &new Folder(basename($j->getCodebase()));
  try(); {
    if (!$folder->exists()) $folder->create();
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  // Download all JAR files, adding them to the classpath as we go
  $classpath= System::getEnv('CLASSPATH');
  $properties= '';
  Console::writeLinef('---> Processing resources from codebase %s', $j->getCodebase());
  foreach ($j->getResources() as $resource) {
    switch (xp::typeOf($resource)) {

      // A JAR file, download it
      case 'com.sun.webstart.jnlp.JnlpJarResource':
        $href= &new URL(strstr($resource->getHref(), '://')
          ? $resource->getHref()
          : $j->getCodebase().'/'.ltrim($resource->getHref(), './')
        );
        
        $classpath.= ':'.rtrim($folder->getURI(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$href->getPath();
        try(); {
          $params= array();

          // Create a new file instance
          $jar= &new File($folder->getURI().DIRECTORY_SEPARATOR.$href->getPath());
          if ($jar->exists()) {
            Console::writef('     >> Have %s... ', basename($jar->getURI()));
            $params['If-Modified-Since']= date('D, d M Y H:i:s \G\M\T', $jar->lastModified());
          }


          // Issue HTTP request
          $c= &new HttpConnection(strstr($resource->getLocation(), '://') 
            ? $resource->getLocation()
            : $j->getCodebase().'/'.ltrim($resource->getLocation(), './')
          );
          $response= &$c->get(NULL, $params);
          Console::write('     << ', $response->getStatuscode(), ' "', $response->getMessage(), '": ');
          
          // Check response code
          switch ($response->getStatusCode()) {
            case 200:
              Console::writeLine();
              Console::writef('     >> Downloading %s... ', $href->getURL());

              // Check if this file resided in a subdirectory. If so, create this
              // subdirectory if necessary
              $f= &new Folder(dirname($jar->getURI()));
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
              Console::writeLinef('FAIL %s... ', $j->getCodebase().'/'.$resource->getLocation());
              break;

            default:
              Console::writeLine('FAIL');
          }
        } if (catch('Exception', $e)) {
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
  $app= &$j->getApplicationDesc();
  $cmd= sprintf(
    '%s %s -cp %s %s %s 2>&1',
    $p->value('java', 'j', 'java'),
    $properties,
    $classpath,
    $app->getMain_Class(),
    implode(' ', $app->getArguments())
  );
  Console::writeLine('---> Executing ', $cmd);

  try(); {
    $p= &new Process($cmd);
    while (!$p->out->eof()) {
      Console::writeLine($p->out->readLine());
    }
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  // Pass exit value to caller
  exit($p->close());
  // }}}
?>
