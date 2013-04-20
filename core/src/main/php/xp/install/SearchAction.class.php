<?php
  namespace xp\install;

  use \util\cmd\Console;
  use \webservices\rest\RestRequest;

  /**
   * XPI Installer - search modules
   * ==============================
   *
   * Basic usage
   * -----------
   * # This will search for modules
   * $ xpi search vendor
   */
  class SearchAction extends Action {

    /**
     * Execute this action
     *
     * @param  string[] $args command line args
     * @return int exit code
     */
    public function perform($args) {
      $request= create(new RestRequest('/search'))->withParameter('q', $args[0]);

      Console::writeLine('@', $this->api->getBase()->getURL());
      $total= 0;
      $results= $this->api->execute($request)->data();
      foreach ($results as $result) {
        Console::writeLine(new Module($result['vendor'], $result['module']), ': ', $result['info']);
        $i++;
      }

      Console::writeLine();
      Console::writeLine($i, ' modules(s) found.');
      return 0;
    }
  }
?>