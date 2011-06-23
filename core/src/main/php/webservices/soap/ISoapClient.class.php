<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * SoapClient interface
   *
   * @purpose   Interface
   */
  interface ISoapClient {

    public function setEncoding($encoding);
    public function getEncoding();

    public function setStyle($style);
    public function getStyle();
    public function setSoapEncoding($encoding);
    public function getSoapEncoding();
    public function setSoapVersion($version);

    public function setWsdl($url);
    public function setEndpoint($url);

    public function registerMapping(Qname $qname, XPClass $class);
  }

?>
