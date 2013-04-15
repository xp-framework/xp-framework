<?php
  namespace xp\install;

  use \io\Folder;
  use \io\collections\FileCollection;
  use \io\collections\iterate\FilteredIOCollectionIterator;
  use \io\collections\iterate\CollectionFilter;
  use \io\streams\StringReader;
  use \util\cmd\Console;
  use \webservices\json\JsonFactory;

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
  class ListAction extends \lang\Object {
    protected static $json;

    static function __static() {
      self::$json= JsonFactory::create();
    }

    /**
     * List all modules of a given vendor
     *
     * @param  io.collections.IOElement $vendor
     * @param  io.collections.IOCollection $cwd
     * @return int
     */
    protected function listModulesOf($vendor, $cwd) {
      $i= 0;
      $meta= self::$json->decodeFrom($vendor->getInputStream());
      foreach (new FilteredIOCollectionIterator($vendor->getOrigin(), new CollectionFilter()) as $dir) {
        sscanf(basename($dir->getURI()), '%[^@]@%s', $name, $version);
        $module= new Module($meta['name'], $name); 

        if ($pth= $cwd->findElement('.'.$module->vendor.'.'.$module->name.'@'.$version.'.pth')) {
          Console::writeLine('+ ', $module, ' @', $version, ' {');
          $r= new StringReader($pth->getInputStream());
          while (NULL !== ($line= $r->readLine())) {
            $resolved= realpath($cwd->getURI().ltrim($line, '!'));
            if (is_dir($resolved)) {
              $cl= \lang\FileSystemClassLoader::instanceFor($resolved, FALSE);
            } else if (is_file($resolved)) {
              $cl= \lang\archive\ArchiveClassLoader::instanceFor($resolved, FALSE);
            }
            Console::writeLine('  ', $cl);
          }
          Console::writeLine('}');
        } else {
          Console::writeLine('- ', $module, ' @', $version);
        }
        $i++;
      }
      return $i;
    }

    /**
     * Execute this action
     *
     * @param  string[] $args command line args
     * @return int exit code
     */
    public function perform($args) {
      $cwd= new FileCollection('.');

      Console::writeLine('@', $cwd->getURI());
      if (isset($args[0])) {
        if ($vendor= $cwd->getCollection($args[0])->findElement('vendor.json')) {
          $total= $this->listModulesOf($vendor, $cwd);
          Console::writeLine();
          Console::writeLine($total, ' module(s) from ', $args[0], ' installed');
        }
      } else {
        $total= 0;
        foreach (new FilteredIOCollectionIterator($cwd, new CollectionFilter()) as $dir) {
          if ($vendor= $dir->findElement('vendor.json')) {
            $total+= $this->listModulesOf($vendor, $cwd);
          }
        }
        Console::writeLine();
        Console::writeLine($total, ' module(s) installed');
      }

      return 0;
    }
  }
?>