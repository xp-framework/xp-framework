<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  $package= 'xp.runtime';
 
  uses('util.Properties', 'util.cmd.Console', 'io.File', 'lang.archive.Archive');

  /**
   * Runs an application from a XAR file
   *
   * @purpose  Tool
   */
  class xp·runtime·Xar extends Object {
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
      $cl= ClassLoader::registerLoader(new ArchiveClassLoader(new Archive($f)));
      if (!$cl->providesResource(self::MANIFEST)) {
        Console::$err->writeLine('*** Archive '.$f->getURI().' does not have a manifest');
        return 126;
      }

      // Load manifest
      $pr= Properties::fromString($cl->getResource(self::MANIFEST));
      if (NULL === ($class= $pr->readString('archive', 'main-class', NULL))) {
        Console::$err->writeLine('*** Archive '.$f->getURI().'\'s manifest does not have a main class');
        return 126;
      }

      // Run main()
      try {
        return XPClass::forName($class, $cl)->getMethod('main')->invoke(NULL, array($args));
      } catch (TargetInvocationException $e) {
        throw $e->getCause();
      }
    }
  }
?>
