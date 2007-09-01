<?php
/* This class is part of the XP framework
 *
 * $Id: GlobalWeather.class.php 10019 2007-04-17 10:02:08Z kiesel $
 */

  namespace com::capeconnect;

  ::uses('webservices.soap.SoapDriver');
  
  /**
   * GlobalWeather is a new and improved version of our popular AirportWeather 
   * Web service. This Web service returns detailed, strong-typed and 
   * time-stamped weather data, and returns results much faster than 
   * AirportWeather. You can search for valid weather stations by station codes, 
   * country, latitude, longitude, elevation, name or region. You can also use 
   * the isValidCode operation to validate a particular code.
   *
   * Example:
   * <code>
   *   $w= new GlobalWeather();
   *   try {
   *     $report= $w->getWeatherReport('FRA');
   *   } catch(XPException $e) {
   *     $e->printStackTrace();
   *     exit;
   *   }
   *   
   *   var_dump($report);
   * </code>
   * 
   * @purpose  Provide an API to Capescience's Weatherservice
   * @see      http://xmethods.net/ve2/ViewListing.po?serviceid=98735
   * @see      http://www.capescience.com/webservices/globalweather/
   * @see      http://www.w3.org/2000/06/webdata/xslt?xslfile=http://www.capescience.com/simplifiedwsdl.xslt&xmlfile=http://live.capescience.com/wsdl/GlobalWeather.wsdl&transform=Submit
   */
  class GlobalWeather extends lang::Object {
    public
      $client = NULL;
      
    /**
     * Constructor
     *
     */
    public function __construct() {
      $this->client= webservices::soap::SoapDriver::getInstance()->forEndpoint(
        'http://live.capescience.com/ccx/GlobalWeather',
        'capeconnect:GlobalWeather:GlobalWeather'
      );
    }

    /**
     * Gets weather report by Airport code
     *
     * @see     http://www.gironet.nl/home/aviator1/iata/iatacode.htm                
     * @see     http://www.wajb.freeserve.co.uk/codes.htm                            
     * @param   string code Airport code, such as "FRA" for Frankfurt/Main, Germany  
     * @return  &lang.Object report                                                        
     */
    public function getWeatherReport($code) {
      return $this->client->invoke('getWeatherReport', new ('code', $code));
    }
  }
?>
