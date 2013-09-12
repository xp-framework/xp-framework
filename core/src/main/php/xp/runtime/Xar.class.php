<?php namespace xp\runtime;

use util\Properties;
use util\cmd\Console;
use io\File;
use lang\archive\Archive;

/**
 * Runs an application from a XAR file
 *
 * @purpose  Tool
 */
class Xar extends \lang\Object {
  const MANIFEST = 'META-INF/manifest.ini';
  
  /**
   * Main
   *
   * Exitcodes used:
   * <ul>
   *   <li>127: Archive referenced in -xar [...] does not exist</li>
   *   <li>126: No manifest or manifest does not have a main-class</li>
   * </ul>
   *
   * @see     http://tldp.org/LDP/abs/html/exitcodes.html
   * @param   string[] args
   * @return  int
   */
  public static function main(array $args) {
  
    // Open archive
    $f= new File(array_shift($args));
    if (!$f->exists()) {
      Console::$err->writeLine('*** Cannot find archive '.$f->getURI());
      return 127;
    }

    // Register class loader
    $cl= \lang\ClassLoader::registerLoader(new \lang\archive\ArchiveClassLoader(new Archive($f)));
    if (!$cl->providesResource(self::MANIFEST)) {
      Console::$err->writeLine('*** Archive '.$f->getURI().' does not have a manifest');
      return 126;
    }

    // Load manifest
    $pr= Properties::fromString($cl->getResource(self::MANIFEST));
    if (null === ($class= $pr->readString('archive', 'main-class', null))) {
      Console::$err->writeLine('*** Archive '.$f->getURI().'\'s manifest does not have a main class');
      return 126;
    }

    // Run main()
    try {
      return \lang\XPClass::forName($class, $cl)->getMethod('main')->invoke(null, array($args));
    } catch (\lang\reflect\TargetInvocationException $e) {
      throw $e->getCause();
    }
  }
}
