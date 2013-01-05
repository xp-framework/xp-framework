<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xml.QName');

  /**
   * SoapClient interface
   *
   */
  interface ISoapClient {

    /**
     * Set encoding
     *
     * @param   string encoding
     */
    public function setEncoding($encoding);

    /**
     * Get encoding
     *
     * @return  string
     */
    public function getEncoding();

    /**
     * Set SOAP style - rpc or document.
     *
     * @param   int style
     */
    public function setStyle($style);

    /**
     * Get SOAP style
     *
     * @return  int
     */
    public function getStyle();

    /**
     * Set SOAP encoding - encoded or literal
     *
     * @param   int encoding
     */
    public function setSoapEncoding($encoding);

    /**
     * Get SOAP encoding
     *
     * @return  int
     */
    public function getSoapEncoding();

    /**
     * Set SOAP version: 1.1 or 1.2
     *
     * @param   int version
     */
    public function setSoapVersion($version);

    /**
     * Enable or disable use of WSDL mode
     *
     * @param   bool enabled
     */
    public function setWsdl($enabled);

    /**
     * Set endpoint URL
     *
     * @param   string url
     */
    public function setEndpoint($url);

    /**
     * Register custom SOAP type mapping based on QName
     *
     * @param   xml.QName qname
     * @param   lang.XPClass class
     */
    public function registerMapping(QName $qname, XPClass $class);

    /**
     * Set connect timeout
     *
     * @param   int
     */
    public function setConnectTimeout($i);

    /**
     * Set connect timeout
     *
     * @return  int
     */
    public function getConnectTimeout();

    /**
     * Set connect timeout
     *
     * @param   int
     */
    public function setTimeout($i);

    /**
     * Set connect timeout
     *
     * @return  int
     */
    public function getTimeout();

    /**
     * Perform SOAP call
     *
     * @param   mixed[]
     * @return  mixed
     */
    public function invoke();
  }

?>
