<?php namespace xp\install;

use io\Folder;
use io\collections\FileCollection;
use io\collections\iterate\FilteredIOCollectionIterator;
use io\collections\iterate\ExtensionEqualsFilter;
use util\cmd\Console;
use webservices\json\JsonFactory;

/**
 * XPI Installer - list modules
 * ============================
 *
 * Basic usage
 * -----------
 * # This will list all installed modules
 * $ xpi list
 *
 * # This will list all installed modules from a given vendor
 * $ xpi list vendor
 */
class ListAction extends Action {
  protected static $json;

  static function __static() {
    self::$json= JsonFactory::create();
  }

  /**
   * Execute this action
   *
   * @param  string[] $args command line args
   * @return int exit code
   */
  public function perform($args) {
    $cwd= new FileCollection('.');
    $isModule= new ExtensionEqualsFilter('.json');

    // If an argument is given, search only that vendor
    if (isset($args[0])) {
      $find= $cwd->getCollection($args[0]);
    } else {
      $find= $cwd;
    }

    $total= 0;
    Console::writeLine('@', $cwd->getURI());
    foreach (new FilteredIOCollectionIterator($find, $isModule, true) as $module) {
      $result= self::$json->decodeFrom($module->getInputStream());
      Console::writeLine(new Module($result['vendor'], $result['module']), ': ', $result['info']);

      $total++;
    }
    Console::writeLine();
    Console::writeLine($total, ' module(s) installed');

    return 0;
  }
}