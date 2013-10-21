<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('webservices.rest.srv.Input', 'webservices.rest.srv.Upload');

  /**
   * Represents multipart/form-data
   *
   * @see   php://is_uploaded_file
   */
  class Uploads extends webservices·rest·srv·Input {

    /**
     * Returns all uploads by a given name
     *
     * @param  string $name
     * @return webservices.rest.srv.Upload[]
     */
    public function eachNamed($name) {
      $r= array();
      $params= $this->request->getParam($name);
      foreach (isset($params['tmp_name']) ? array($params) : $params as $param) {
        if (!is_uploaded_file($param['tmp_name'])) {
          throw new IllegalArgumentException('Parameter "'.$name.'" is not an uploaded file!');
        }
        $r[]= new Upload($param);
      }
      return $r;
    }
  }
?>
