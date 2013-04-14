<?php
  namespace xp\install;

  use \webservices\rest\RestClient;
  use \webservices\rest\RestRequest;
  use \util\cmd\Console;
  use \io\Folder;
  use \io\File;

  class XarRelease extends \lang\Object implements Origin {
    private $client;
    private $release;

    /**
     * Creates a new instance
     *
     * @param string $vendor
     * @param string $module
     * @param string $branch
     */
    public function __construct($vendor, $module, $branch) {
      $this->client= new RestClient('http://builds.planet-xp.net/');
      $this->release= new RestRequest(sprintf(
        '%s/%s/%s',
        $vendor,
        $module,
        $branch
      ));
    }

    /**
     * Fetches this origin into a given target folder
     *
     * @param  io.Folder $target
     */
    public function fetchInto(Folder $target) {
      $r= $this->client->execute($this->release);
      if (200 !== $r->status()) {
        throw new \lang\IllegalArgumentException($r->message().': '.$this->resource->toString());
      }
      $release= $r->data();

      // Download files
      $pth= create(new File($target, 'class.pth'))->getOutputStream();
      foreach ($release['files'] as $file) {
        $d= $this->client->execute(new RestRequest($this->release->getResource().'/'.$file['name']));
        Console::writeLine('>> ', $file['name']);

        $f= new File($target, $file['name']);
        with ($out= $f->getOutputStream()); {
          $out->write($d->content());
          $out->close();
        }
        $pth->write($file['name']."\n");
      }
      $pth->close();
    }
  }
?>