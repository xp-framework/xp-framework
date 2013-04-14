<?php
  namespace xp\install;

  use \io\Folder;
  use \io\collections\FileCollection;
  use \io\collections\iterate\FilteredIOCollectionIterator;
  use \io\collections\iterate\CollectionFilter;
  use \util\cmd\Console;
  use \webservices\json\JsonFactory;

  /**
   * List installed modules
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

        if ($cwd->findElement('.'.$meta['name'].'.'.$name.($version ? '@'.$version : '').'.pth')) {
          Console::write('A ');
        } else {
          Console::write('- ');
        }

        Console::writeLine(new Module($meta['name'], $name), ' @', $version);
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