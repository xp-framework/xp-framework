<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.telephony.TelephonyAddress',
    'lang.IllegalArgumentException'
  );

  define ('TAP_MIN_SUBSCRIBER_NUMBER', 4);

  /**
   * This class parses an telephone number. Its main purpose
   * is to bring phone numbers into a normalized form.
   *
   * @purpose Parse any string into a TelephonyAddress object
   * @see     http://www.wtng.info/
   * @see     http://www.construction-site.com/int_dial.htm
   */
  class TelephonyAddressParser extends Object {
    public 
      $defaultCountryCode=  NULL,
      $defaultAreaCode=     NULL,
      $defaultSubscriber=   NULL;

    public $intDialCodes= array (
      '93'       => 'Afghanistan',               
      '355'      => 'Albania',                  
      '213'      => 'Algeria',                  
      '376'      => 'Andorra',                  
      '244'      => 'Angola',                   
      '1 809'    => 'Anguilla',               
      '672'      => 'Antarctic Aus Territory',  
      '1 809'    => 'Antigua and Barbuda',    
      '599'      => 'Antilles',                 
      '54'       => 'Argentina',                 
      '374'      => 'Armenia',                  
      '297'      => 'Aruba',                    
      '247'      => 'Ascension Island',         
      '61'       => 'Australia',                 
      '43'       => 'Austria',                   
      '994'      => 'Azerbaijan',               
      '351'      => 'Azores',                   
      '1809'     => 'Bahamas',                 
      '973'      => 'Bahrain',                  
      '890'      => 'Bangladesh',               
      '809'      => 'Barbados',                 
      '375'      => 'Belarus',                  
      '32'       => 'Belgium',                   
      '501'      => 'Belize',                   
      '229'      => 'Belize',                   
      '1 809'    => 'Bermuda',                
      '975'      => 'Bhutan',                   
      '591'      => 'Bolivia',                  
      '387'      => 'Bosnia Hercegovina',       
      '267'      => 'Botswana',                 
      '55'       => 'Brazil',                    
      '673'      => 'Brunei Darussalam',        
      '359'      => 'Bulgaria',                 
      '226'      => 'Bukina Faso',              
      '257'      => 'Barundi',                  
      '855'      => 'Cambodia',                 
      '237'      => 'Cameroon',                 
      '1'        => 'Canada',                     
      '238'      => 'Cape Verde Islands',       
      '1 809'    => 'Cayman Islands',         
      '236'      => 'Central African Republic', 
      '235'      => 'Chad',                     
      '56'       => 'Chile',                     
      '86'       => 'China',                     
      '672'      => 'Christmas Island',         
      '672'      => 'Cocos Island',             
      '57'       => 'Columbia',                  
      '269'      => 'Comoros',                  
      '242'      => 'Congo',                    
      '682'      => 'Cook Islands',             
      '506'      => 'Costa Rica',               
      '225'      => 'Cote d\'Ivorie',           
      '385'      => 'Croatia',                  
      '53'       => 'Cuba',                      
      '357'      => 'Cyprus',                   
      '42'       => 'Czech Republic',            
      '45'       => 'Denmark',                   
      '253'      => 'Djibouti',                 
      '1 809'    => 'Dominica',               
      '1 809'    => 'Dominican Rebublic',     
      '593'      => 'Ecuador',                  
      '20'       => 'Egypt',                     
      '503'      => 'El Salvador',              
      '240'      => 'Equatorial Guinea',        
      '291'      => 'Eritrea',                  
      '372'      => 'Estonia',                  
      '251'      => 'Ethiopia',                 
      '500'      => 'Falkland Islands',         
      '298'      => 'Faroe Islands',            
      '679'      => 'Fiji',                     
      '358'      => 'Finland',                  
      '33'       => 'France',                    
      '594'      => 'French Guiana',            
      '689'      => 'French Polynesia',         
      '241'      => 'Gabon',                    
      '220'      => 'Gambia',                   
      '49'       => 'Germany',                   
      '233'      => 'Ghana',                    
      '350'      => 'Gibraltar',                
      '30'       => 'Greece',                    
      '299'      => 'Greenland',                
      '1 809'    => 'Grenada',                
      '590'      => 'Guadeloupe',               
      '671'      => 'Guam',                     
      '502'      => 'Guatemala',                
      '224'      => 'Guinea',                   
      '245'      => 'Guinea - Bissau',          
      '592'      => 'Guyana',                   
      '509'      => 'Haiti',                    
      '504'      => 'Honduras',                 
      '852'      => 'Hong Kong',                
      '36'       => 'Hungary',                   
      '354'      => 'Iceland',                  
      '91'       => 'India',                     
      '62'       => 'Indonesia',                 
      '98'       => 'Iran',                      
      '964'      => 'Iraq',                     
      '353'      => 'Ireland Republic of',      
      '972'      => 'Israel',                   
      '39'       => 'Italy',                     
      '225'      => 'Ivory Coast',              
      '1 809'    => 'Jamaica',                
      '81'       => 'Japan',                     
      '962'      => 'Jordan',                   
      '7'        => 'Kazakhstan',                 
      '254'      => 'Kenya',                    
      '7'        => 'Kirghizstan',                
      '686'      => 'Kiribati',                 
      '850'      => 'Korea (North)',            
      '82'       => 'Korea (South)',             
      '965'      => 'Kuwait',                   
      '856'      => 'Laos',                     
      '371'      => 'Latvia',                   
      '961'      => 'Lebanon',                  
      '266'      => 'Lesotho',                  
      '231'      => 'Liberia',                  
      '218'      => 'Lybia',                    
      '423'      => 'Liechtenstein',            
      '370'      => 'Lithuania',                
      '352'      => 'Luxembourg',               
      '853'      => 'Macao',                    
      '389'      => 'Macedonia',                
      '261'      => 'Madagascar',               
      '265'      => 'Malawi',                   
      '60'       => 'Malaysia',                  
      '960'      => 'Maldives',                 
      '223'      => 'Mali',                     
      '356'      => 'Malta',                    
      '692'      => 'Marshall Islands',         
      '596'      => 'Martinique',               
      '222'      => 'Mauritania',               
      '230'      => 'Mauritius',                
      '269'      => 'Mayotte',                  
      '52'       => 'Mexico',                    
      '691'      => 'Micronesia',               
      '373'      => 'Moldovia',                 
      '33 93'    => 'Monaco',                 
      '976'      => 'Mongolia',                 
      '1 809'    => 'Montserrat',             
      '212'      => 'Morocco',                  
      '258'      => 'Mozanbique',               
      '95'       => 'Myanmar (Burma)',           
      '264'      => 'Namibia',                  
      '674'      => 'Nauru',                    
      '977'      => 'Napal',                    
      '31'       => 'Netherlands (Holland)',     
      '599'      => 'Netherlands Antilles',     
      '687'      => 'New Caledonia',            
      '505'      => 'Nicaragua',                
      '227'      => 'Niger',                    
      '234'      => 'Nigeria',                  
      '47'       => 'Norway',                    
      '968'      => 'Oman',                     
      '92'       => 'Pakistan',                  
      '507'      => 'Panama',                   
      '675'      => 'Papua New Guinea',         
      '595'      => 'Paraguay',                 
      '51'       => 'Peru',                      
      '63'       => 'Philippines',               
      '649'      => 'Pitcain Island',           
      '48'       => 'Poland',                    
      '361'      => 'Portugal',                 
      '1 809'    => 'Pueto Ricco',            
      '974'      => 'Qatar',                    
      '40'       => 'Romania',                   
      '7'        => 'Russia',                     
      '250'      => 'Rwanda',                   
      '290'      => 'St Helena',                
      '1 809'    => 'St Kitts and Nevis',     
      '685'      => 'Samoa (USA)',              
      '685'      => 'Samoa Western',            
      '378'      => 'San Marino',               
      '966'      => 'Saudi Arabia',             
      '221'      => 'Senegal',                  
      '248'      => 'Seychelles',               
      '232'      => 'Sierra Leone',             
      '65'       => 'Singapore',                 
      '42'       => 'Slovakia',                  
      '386'      => 'Slovenia',                 
      '677'      => 'Solom Islands',            
      '252'      => 'Somalia',                  
      '27'       => 'South Africa',              
      '34'       => 'Spain',                     
      '94'       => 'Sri Lanka',                 
      '249'      => 'Sudan',                    
      '597'      => 'Surinam',                  
      '268'      => 'Swaziland',                
      '46'       => 'Sweden',                    
      '41'       => 'Switzerland',               
      '963'      => 'Syria',                    
      '886'      => 'Taiwan',                   
      '7'        => 'Tajikistan',                 
      '255'      => 'Tanzania',                 
      '66'       => 'Thailand',                  
      '228'      => 'Togo',                     
      '676'      => 'Tongo',                    
      '1 809'    => 'Trinidad & Tobago',      
      '216'      => 'Tunisia',                  
      '90'       => 'Turkey',                    
      '7'        => 'Turkmenistan',               
      '1 809'    => 'Turks & Caicos',         
      '688'      => 'Tuvalu',                   
      '256'      => 'Uganda',                   
      '380'      => 'Ukraine',                  
      '971'      => 'United Arab Emirates',     
      '44'       => 'United Kingdom',            
      '598'      => 'Uraguay',                  
      '1'        => 'USA',                        
      '7'        => 'Uzbekistan',                 
      '678'      => 'Vanuatu',                  
      '58'       => 'Venezuela',                 
      '84'       => 'Vietnam',                   
      '1 809 49' => 'Virgin Islands (UK)', 
      '1 809'    => 'Virgin Islands (US)',    
      '967'      => 'Yemen',                    
      '381'      => 'Yugoslavia',               
      '243'      => 'Zaire',                    
      '260'      => 'Zambia',                   
      '263'      => 'Zimbabwe'                  
    );
      
    /**
     * Creates a PhoneNumber object
     *
     * @param   array defaults array with default values (may be omitted)
     */      
    public function __construct($params= NULL) {
      parent::__construct($params);
    }

    /**
     * Prepare the given string for futher processing. This function
     * converts any alphabetical char into a corresponding number, to
     * allow phone strings like '0700-NEEDGIRL'.
     *
     * @param   string phonestring
     * @return  string phonestring
     */
    protected function _prepare($string) {
      static $letters= array(
        '/a|b|c/',
        '/d|e|f/',
        '/g|h|i/',
        '/j|k|l/',
        '/m|n|o/',
        '/p|q|r|s/',
        '/t|u|v/',
        '/w|x|y|z/'
      );
      static $numbers= array ('2', '3', '4', '5', '6', '7', '8', '9');
                       
      return preg_replace('/(\D)\D*/', '\1', preg_replace(
        $letters, 
        $numbers, 
        strtolower($string)
      ));
    }
    
    /**
     * Parses a phone number. This relies on the following facts:
     * Addresses must have a certain minimal length, otherwise they
     * are interpreted as extensions only. If they have sufficient length
     * they are parsed from left to right, first searched for international
     * country codes which are diffed agains an internal list. Then
     * the national prefix is searched - it *must* be divided by a
     * non-decimal from the subscriber number. As this class cannot have
     * a list of allowed national area codes the division is absolutely
     * neccessary.
     *
     * @param   string phonenumber
     * @return  TelephonyAddress obj
     * @throws  lang.FormatException if number is malformed
     * @throws  lang.IllegalStateException in case no default number has been set
     */
    public function parseNumber($number) {
      // Check current state
      if (
        NULL === $this->defaultCountryCode ||
        NULL === $this->defaultAreaCode ||
        NULL === $this->defaultSubscriber
      ) {
        throw (new IllegalStateException ('At least one of the default numbers has not been set. Set them before parsing'));
      }

      // Parsing
      $a= new TelephonyAddress();
      $a->setType (TEL_ADDRESS_INTERNATIONAL);
      
      $number= $this->_prepare ($number);

      // Numbers smaller than this are extensions only
      if (strlen ($number) < TAP_MIN_SUBSCRIBER_NUMBER) {
        $a->setCountryCode ($this->defaultCountryCode);
        $a->setAreaCode ($this->defaultAreaCode);
        $a->setSubscriber ($this->defaultSubscriber);
        $a->setExt ($number);
        $a->setType (TEL_ADDRESS_INTERNAL);
        return $a;
      }
        
      $number= preg_replace ('/^00/', '+', $number);
      
      // Find out international dial country code
      if ('+' == $number{0}) {
        foreach ($this->intDialCodes as $dial => $countryName) {
          if (($prefix= '+'.str_replace (' ', '', $dial)) == substr ($number, 0, strlen ($prefix))) {
            $a->setCountryCode ($prefix);
          }
        }
        
        $number= '0'.trim (substr ($number, strlen ($prefix)-1));
      } else {
        $a->setCountryCode ($this->defaultCountryCode);
      }

      // Find out area code
      if ('0' == $number{0} && preg_match ('/^(\d+)/', $number, $match)) {
        $a->setAreaCode ($match[1]);
        $number= substr ($number, strlen ($match[1]));
        
        // Remove any remaining dividers from the beginning
        $number= preg_replace ('/^([^\d]*)/', '', $number);
      } else {
        $a->setAreaCode ($this->defaultAreaCode);
      }
      
      // Check whether we have a phone-ext
      for ($i= strlen ($number)-1; $i >= 0 && is_numeric ($number{$i}); $i--) {}

      if (0 <= $i && $i >= TAP_MIN_SUBSCRIBER_NUMBER) {
        $ext= substr ($number, $i+1);
        $a->setExt ($ext);
        $number= substr ($number, 0, $i);
      }
      
      $number= preg_replace ('/[^\d]/', '', $number);
      if (strlen ($number) < 3)
        throw (new FormatException ('No parseable phone number'));
    
      $a->setSubscriber ($number);
      return $a;
    }
  }
?>
