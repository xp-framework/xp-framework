<?php
  namespace xp\install;

  use \util\cmd\Console;
  use \webservices\rest\RestClient;
  use \webservices\rest\RestRequest;
  use \webservices\rest\RestException;

  /**
   * XPI Installer - search modules
   * ==============================
   *
   * Basic usage
   * -----------
   * # This will search for modules
   * $ xpi search vendor
   */
  class SearchAction extends \lang\Object {

    /**
     * Execute this action
     *
     * @param  string[] $args command line args
     * @return int exit code
     */
    public function perform($args) {
      $rest= new RestClient('http://localhost:8080/');
      $request= create(new RestRequest('/search'))->withParameter('q', $args[0]);

      $i= 0;
      $results= $rest->execute($request)->data();
      foreach ($results as $result) {
        Console::writeLine(new Module($result['vendor'], $result['module']));
        $i++;
      }

      Console::writeLine();
      Console::writeLine($i, ' result(s) found.');
      return 0;
    }
  }
?>