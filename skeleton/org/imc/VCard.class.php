<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('text.parser.VFormatParser', 'util.Date');
  
  // Identifier
  define('VCARD_ID',             'VCARD');

  // Property params
  define('VCARD_TEL_TYPE_FAX',   'FAX');		    
  define('VCARD_TEL_TYPE_VOICE', 'VOICE');  	    
  define('VCARD_TEL_LOC_WORK',   'WORK');		    
  define('VCARD_TEL_LOC_HOME',   'HOME');		    
  define('VCARD_TEL_LOC_CELL',   'CELL');		    
  define('VCARD_ADR_HOME',       'HOME');		    
  define('VCARD_ADR_WORK',       'WORK');	
  define('VCARD_ADR_POSTAL',     'POSTAL');	    

  /**
   * VCard
   *
   * <quote>
   * vCard automates the exchange of personal information typically found on a 
   * traditional business card. vCard is used in applications such as Internet
   * mail, voice mail, Web browsers, telephony applications, call centers, 
   * video conferencing, PIMs (Personal Information Managers), PDAs (Personal 
   * Data Assistants), pagers, fax, office equipment, and smart cards. vCard 
   * information goes way beyond simple text, and includes elements like 
   * pictures, company logos, live Web addresses, and so on.
   * </quote>
   *
   * Example:
   * <pre>
   * BEGIN:VCARD
   * N:Internet Mail Consortium
   * FN:Internet Mail Consortium
   * ORG:Internet Mail Consortium;
   * EMAIL;INTERNET;WORK:phoffman@imc.org
   * TEL;WORK;VOICE:+1 831 426 9827
   * TEL;WORK;FAX:+1 831 426 7301
   * ADR;POSTAL:;;127 Segre Place;Santa Cruz;CA;95060;USA
   * LABEL;POSTAL;DOM;ENCODING=QUOTED-PRINTABLE:127 Segre Place=0D=0A=
   *      Santa Cruz, CA  95060=0D=0A                        USA
   * URL:http://www.imc.org/
   * TZ:-08:00
   * LOGO;GIF;ENCODING=BASE64:
   *     R0lGODlhogBNAPEAAP////+AgP8AAAAAACH5BAEAAAAALAAAAACiAE0AAAL/BISpy+1i
   *     opy02ouz3rzTB4aMR5bmiXriCqbuC8cVSy/yjeddzev+/+PRgMRiTLgyKpckZIgJjWKc
   *     D6n1eqCOsFyo1tYNG7+J0+B8FqszZEgJDV/LP18T/D7PG+qku18/p2XnFwcoRzVImGa4
   *     5mSmuMiohvQIKXlYk2J52TjkQsgZyAKDF4opYpp6kqTauvPkGrsBK1tr0WKbO+Og2yvB
   *     6xucBfYT+TZQobihHLOJspXz11coAWlMYY2smX2toiCTrRFeza1Nzl1ZfhKgEPBZbo6t
   *     bgB/XR+/fI/fEcCO0L8N3QV49PRF0LdvIEIT/fw1DDjOAkGEaSjmK3UOo4eG/+w4NmMm
   *     bt7BehMiKtRYktpGjiw/guIgMGXMjC8lSjuZUAPLnaRAhvQpzxrOm0FVniyxk+cLZz+J
   *     2gQqs2ZUo0+RJm35TmoGpk+dJoNa0OvXnBeuKoWIEgPXsWKnUgUAdiwJs2fTpR361i2a
   *     pnfjBl1J1yPavGzv6u1WtK/WG4HrHluMd+9WvyMXr43R2PE0yF0N0yRc2etlGJk1dxhd
   *     GHTYtp8lt0b8onRSu6pfkw3tGTdV1Chkz07EOnFt3qvTEmfo2zTf2rpdR4ZtW7hz0smV
   *     T+acenp26M27CZVR/beH491vFx8O9Tv18FjHUz5sHu57+ZbJc2AvHub88su1S//3bp9O
   *     +FnXGXPn+Qdff4ipl8KA+SkYH3/XBXfgggGW5SCB/0VYYXzHXWYSchm2B+Fp830YV4iA
   *     jSjYRRRGN2FuHRb4ogUsPvgch/S9iCJ2MyVDwY040shdgjGqxiB8iu0j5JBKRqiibdxF
   *     2aFIETTppEVSbqLllrtRxCSWpnVZJZcLPZnXmRKIOSSZO5L0JkFoIhjnhcLokeSd8ugJ
   *     Vy/m6AgFoBJt5YOgyxykRjyGvlEoEIs2Co42Ff3pmmSTyofpOat9lak9kXyq1qeWImMM
   *     NYVcGsciqkpKKqsljUQPogXNmimtrnYaqq2yghorp32KqmufsQJbq7C9GktpsMeL/imr
   *     scLiw6yzkqrVbELROlvNq9kie2y3z2q7bLXijsvted5GK81t11JaabPgnhtuWPBiqyi5
   *     625bbr74xmsttfzie+279MabLbTzEnxvtwkjiy64t3470LirkpsSObCWyyyxBku8MLv0
   *     0drpqB6HXKq1E5OM6V4InlrpxKa2DG2ryU56a5F8ykIACgA7
   * 
   * REV:19970726T000001
   * VERSION:2.1
   * END:VCARD
   * </pre>
   *
   * @see      rfc://2425
   * @see      rfc://2426
   * @see      http://www.imc.org/pdi/
   * @see      http://www.imc.org/pdi/pdiproddev.html
   * @purpose  Handle vCard
   */
  class VCard extends Object {
    var
      $name            	= array(),
      $address        	= array(),
      $email        	= array(),
      $phone        	= array(),
      $organization    	= array(),
      $logo             = array(),
      $birthday         = NULL,
      $fullname        	= '',
      $title        	= '',
      $url            	= '',
      $nick            	= '';
      
    /**
     * (Insert method's description here)
     *
     * @access  protected
     * @param   array keys
     * @param   mixed value
     * @return  
     */
    function addProperty($keys, $value) {
      #ifdef DEBUG
      #echo $this->getClassName().'::addProperty(';
      #var_export($keys);
      #echo ', ';
      #var_export($value);
      #echo ")\n";
      #endif
      
      switch ($keys[0]) {
        case 'LOGO':
          $this->logo= array(
            'format'    => $keys[1],
            'data'      => $value
          );
          break;
          
        case 'BDAY':
          $this->birthday= &new Date($value);
          break;
        
        case 'EMAIL':
          $this->email[isset($keys[2]) ? strtolower($keys[2]) : 'default'][]= $value;
          break;
          
        case 'URL':
          $this->url= $value;
          break;
          
        case 'ORG':
          $this->organization= explode(';', $value);
          break;
          
        case 'TITLE':
          $this->title= $value;
          break;
          
        case 'NICKNAME':
          $this->nick= $value;
          break;
          
        case 'TEL':
          switch ($keys[1]) {
            case VCARD_TEL_LOC_WORK: 
            case VCARD_TEL_LOC_HOME: 
            case VCARD_TEL_LOC_CELL: 
              $this->phone[strtolower($keys[1])][isset($keys[2]) ? strtolower($keys[2]) : 'default']= $value;
              break;
              
            default: 
              return throw(new FormatException($keys[1].' is not a recognized phone type'));
          }
          break;
        
        case 'FN':
          $this->fullname= $value;
          break;
        
        case 'N':
          $values= explode(';', $value);
          $this->name= array(
            'first'      => $values[0],        // First name
            'last'       => $values[1],        // Last name
            'middle'     => $values[2],        // Middle initial
            'title'      => $values[3],        // Title
            'suffix'     => $values[4]         // Suffix
          );
          break;

        case 'ADR':
          switch ($keys[1]) {
            case VCARD_ADR_HOME: $loc= 'home'; break;
            case VCARD_ADR_WORK: $loc= 'work'; break;
            case VCARD_ADR_POSTAL: $loc= 'postal'; break;
            default: 
              return throw(new FormatException($keys[1].' is not a recognized address type'));
          }
          
          $values= explode(';', $value);
          $this->address[$loc]= array(
            'pobox'      => $values[0],        // P.O. Box
            'suffix'     => $values[1],        // Suffix
            'street'     => $values[2],        // Street
            'city'       => $values[3],        // City
            'province'   => $values[4],        // Province
            'zip'        => $values[5],        // Zipcode
            'country'    => $values[6]         // Country
          );
          
          break;
          
        default:
          // Discard
      }      
    }
    
    /**
     * Creata a vCard from a stream
     *
     * <code>
     *   try(); {
     *     $vcard= &VCard::fromStream(new File('/tmp/imc.vcf'));
     *   } if (catch('Exception', $e)) {
     *     $e->printStackTrace();
     *     exit(-1);
     *   }
     *   
     *   var_dump($vcard);
      * </code>
     *
     * @model   static
     * @access  public
     * @param   &io.Stream stream
     * @return  &org.imc.VCard
     */
    function &fromStream(&$stream) {
      $card= &new VCard();
      
      $p= &new VFormatParser(VCARD_ID);
      $p->setDefaultHandler(array(&$card, 'addProperty'));
      
      try(); {
        $p->parse($stream);
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      
      return $card;
    }
    
    /**
     * Returns the textual representation of this vCard
     *
     * <code>
     *   [...]
     *   $f= &new File('me.vcf');
     *   $f->open(FILE_MODE_WRITE);
     *   $f->write($card->export());
     *   $f->close();
     * </code>
     *
     * @access  
     * @param   
     * @return  
     */
    function export() {
      
    }
  
  }
?>
