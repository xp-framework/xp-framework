<?php namespace xp\install;

use io\Folder;
use io\File;
use io\IOException;
use io\collections\FileCollection;
use io\collections\iterate\FilteredIOCollectionIterator;
use io\collections\iterate\ExtensionEqualsFilter;
use io\collections\iterate\NameMatchesFilter;
use io\streams\StringReader;
use util\cmd\Console;
use webservices\json\JsonFactory;
use webservices\rest\RestClient;
use webservices\rest\RestRequest;
use webservices\rest\RestException;

/**
 * XPI Installer - upgrade modules
 * ===============================
 *
 * Basic usage
 * -----------
 * Given there is a newer version available in the registry, this will
 * upgrade the local installation to that.
 * $ xpi upgrade vendor/module
 *
 * Using versions
 * --------------
 * Given the installed version is 1.0.0, the following will reinstall 
 * the module (same as "remove" && "add" with the given version):
 * $ xpi upgrade vendor/module 1.0.0
 *
 * The following will upgrade the installed module to 1.2.3RC1:
 * $ xpi upgrade vendor/module 1.2.3RC1
 *
 * The following will downgrade the installed module to 0.9.1:
 * $ xpi upgrade vendor/module 0.9.1
 */
class UpgradeAction extends Action {
  protected static $json;

  static function __static() {
    self::$json= JsonFactory::create();
  }

  /**
   * Finds all installed releases
   *
   * @param  io.Folder $cwd Current working directory
   * @param  xp.install.Module $module
   * @return [:io.collections.FileCollection] The releases
   */
  protected function installedReleasesOf($cwd, $module) {
    $find= new NameMatchesFilter('/^'.$module->name.'@.+/');
    $base= new FileCollection(new Folder($cwd, $module->vendor));
    $releases= array();
    foreach (new FilteredIOCollectionIterator($base, $find) as $installed) {
      sscanf(basename($installed->getURI()), '%[^@]@%[^/\\]', $name, $version);
      $releases[$version]= $installed;
    }
    return $releases;
  }

  /**
   * Execute this action
   *
   * @param  string[] $args command line args
   * @return int exit code
   */
  public function perform($args) {
    if (empty($args)) {
      Console::$err->writeLine('*** Missing argument #1: Module name');
      return 2;
    }

    sscanf($args[0], '%[^@]@%s', $name, $version);
    $module= Module::valueOf($name);

    $cwd= new Folder('.');
    if (null === $version) {

      // No version given: Search for newest version.
      $releases= $this->installedReleasesOf($cwd, $module);
      if (empty($releases)) {
        Console::$err->writeLine('*** Cannot find installed module ', $module);
        return 3;
      }

      uksort($releases, function($a, $b) { return version_compare($a, $b, '<'); });
      $version= key($releases);
      $installed= $releases[$version];

      Console::writeLine('Using newest installed version ', $version, ' -> ', $installed);
    } else {

      // Version given, select this for upgrading.
      $folder= new Folder($cwd, $module->vendor, $module->name.'@'.$version);
      if (!$folder->exists()) {
        Console::$err->writeLine('*** Cannot find installed module ', $module, ' in version ', $version);
        return 3;
      }

      $installed= new FileCollection($folder);
      Console::writeLine('Using specified version ', $version, ' -> ', $installed);
    }

    if (isset($args[1])) {

      // Target version given, check it exists
      $request= create(new RestRequest('/vendors/{vendor}/modules/{module}/releases/{release}'))
        ->withSegment('vendor', $module->vendor)
        ->withSegment('module', $module->name)
        ->withSegment('release', $args[1])
      ;
      try {
        $release= $this->api->execute($request)->data();
      } catch (RestException $e) {
        Console::$err->writeLine('*** Cannot find module ', $module, '@', $args[1], ': ', $e->getMessage());
        return 3;
      }

      $target= $release['version']['number'];
      if (version_compare($version, $target, '>')) {
        Console::writeLine('>> Local ', $version, ' newer than ', $target, ', doing a downgrade');
      } else if (version_compare($version, $target, '=')) {
        Console::writeLine('>> Local ', $version, ' same as ', $target, ', performing reinstall');
      } else {
        Console::writeLine('>> Local ', $version, ' older than ', $target, ', upgrading');
      }
    } else {

      // No target version given: Check for module online, and upgrade to newest
      $request= create(new RestRequest('/vendors/{vendor}/modules/{module}/releases'))
        ->withSegment('vendor', $module->vendor)
        ->withSegment('module', $module->name)
      ;
      try {
        $releases= $this->api->execute($request)->data();
        usort($releases, function($a, $b) { 
          return version_compare($a['version']['number'], $b['version']['number'], '<'); 
        });
      } catch (RestException $e) {
        Console::$err->writeLine('*** Cannot find module ', $module, ': ', $e->getMessage());
        return 3;
      }

      // Verify we actually need an upgrade
      $target= $releases[0]['version']['number'];
      if (version_compare($version, $target, '>=')) {
        Console::writeLine('*** Already at newest version, no upgrade required: ', $releases[0]);
        return 1;
      }
      Console::writeLine('>> Upgrading to ', $target);
    }

    // Remove old one first, then add newer version
    $args= array($module->vendor.'/'.$module->name.'@'.$version);
    $r= $this->spawn(new RemoveAction())->perform($args);
    if (0 !== $r) return $r;

    $args= array($module->vendor.'/'.$module->name.'@'.$target);
    $r= $this->spawn(new AddAction())->perform($args);
    if (0 !== $r) return $r;
    return 0;
  }
}