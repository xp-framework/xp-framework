<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.Enum');

  /**
   * Currency enumeration. Currencies are identified by their ISO 4217 
   * currency codes.
   *
   * @see      http://www.xe.com/iso4217.php ISO 4217 Currency Code List
   */
  class Currency extends Enum {
    public static
      $AED, $AFN, $ALL, $AMD, $ANG, $AOA, $ARS, $AUD, $AWG, $AZN,
      $BAM, $BBD, $BDT, $BGN, $BHD, $BIF, $BMD, $BND, $BOB, $BRL, $BSD, $BTN, $BWP, $BYR, $BZD,
      $CAD, $CDF, $CHF, $CLP, $CNY, $COP, $CRC, $CUP, $CVE, $CYP, $CZK, 
      $DJF, $DKK, $DOP, $DZD, 
      $EEK, $EGP, $ERN, $ETB, $EUR,
      $FJD, $FKP,
      $GBP, $GEL, $GGP, $GHS, $GIP, $GMD, $GNF, $GTQ, $GYD,
      $HKD, $HNL, $HRK, $HTG, $HUF, 
      $IDR, $ILS, $IMP, $INR, $IQD, $IRR, $ISK,
      $JEP, $JMD, $JOD, $JPY, 
      $KES, $KGS, $KHR, $KMF, $KPW, $KRW, $KWD, $KYD, $KZT,
      $LAK, $LBP, $LKR, $LRD, $LSL, $LTL, $LVL, $LYD, 
      $MAD, $MDL, $MGA, $MKD, $MMK, $MNT, $MOP, $MRO, $MTL, $MUR, $MVR, $MWK, $MXN, $MYR, $MZN,
      $NAD, $NGN, $NIO, $NOK, $NPR, $NZD,
      $OMR,
      $PAB, $PEN, $PGK, $PHP, $PKR, $PLN, $PYG, 
      $QAR,
      $RON, $RSD, $RUB, $RWF, 
      $SAR, $SBD, $SCR, $SDG, $SEK, $SGD, $SHP, $SLL, $SOS, $SPL, $SRD, $STD, $SVC, $SYP, $SZL,
      $THB, $TJS, $TMM, $TND, $TOP, $TRY, $TTD, $TVD, $TWD, $TZS,
      $UAH, $UGX, $USD, $UYU, $UZS, 
      $VEB, $VEF, $VND, $VUV, $WST,
      $XAF, $XAG, $XAU, $XCD, $XDR, $XOF, $XPD, $XPF, $XPT,
      $YER,
      $ZAR, $ZMK, $ZWD;
    
    static function __static() {
      self::$AED= new self(0,   'AED');
      self::$AFN= new self(1,   'AFN');
      self::$ALL= new self(2,   'ALL');
      self::$AMD= new self(3,   'AMD');
      self::$ANG= new self(4,   'ANG');
      self::$AOA= new self(5,   'AOA');
      self::$ARS= new self(6,   'ARS');
      self::$AUD= new self(7,   'AUD');
      self::$AWG= new self(8,   'AWG');
      self::$AZN= new self(9,   'AZN');
      self::$BAM= new self(10,  'BAM');
      self::$BBD= new self(11,  'BBD');
      self::$BDT= new self(12,  'BDT');
      self::$BGN= new self(13,  'BGN');
      self::$BHD= new self(14,  'BHD');
      self::$BIF= new self(15,  'BIF');
      self::$BMD= new self(16,  'BMD');
      self::$BND= new self(17,  'BND');
      self::$BOB= new self(18,  'BOB');
      self::$BRL= new self(19,  'BRL');
      self::$BSD= new self(20,  'BSD');
      self::$BTN= new self(21,  'BTN');
      self::$BWP= new self(22,  'BWP');
      self::$BYR= new self(23,  'BYR');
      self::$BZD= new self(24,  'BZD');
      self::$CAD= new self(25,  'CAD');
      self::$CDF= new self(26,  'CDF');
      self::$CHF= new self(27,  'CHF');
      self::$CLP= new self(28,  'CLP');
      self::$CNY= new self(29,  'CNY');
      self::$COP= new self(30,  'COP');
      self::$CRC= new self(31,  'CRC');
      self::$CUP= new self(32,  'CUP');
      self::$CVE= new self(33,  'CVE');
      self::$CYP= new self(34,  'CYP');
      self::$CZK= new self(35,  'CZK');
      self::$DJF= new self(36,  'DJF');
      self::$DKK= new self(37,  'DKK');
      self::$DOP= new self(38,  'DOP');
      self::$DZD= new self(39,  'DZD');
      self::$EEK= new self(40,  'EEK');
      self::$EGP= new self(41,  'EGP');
      self::$ERN= new self(42,  'ERN');
      self::$ETB= new self(43,  'ETB');
      self::$EUR= new self(44,  'EUR');
      self::$FJD= new self(45,  'FJD');
      self::$FKP= new self(46,  'FKP');
      self::$GBP= new self(47,  'GBP');
      self::$GEL= new self(48,  'GEL');
      self::$GGP= new self(49,  'GGP');
      self::$GHS= new self(50,  'GHS');
      self::$GIP= new self(51,  'GIP');
      self::$GMD= new self(52,  'GMD');
      self::$GNF= new self(53,  'GNF');
      self::$GTQ= new self(54,  'GTQ');
      self::$GYD= new self(55,  'GYD');
      self::$HKD= new self(56,  'HKD');
      self::$HNL= new self(57,  'HNL');
      self::$HRK= new self(58,  'HRK');
      self::$HTG= new self(59,  'HTG');
      self::$HUF= new self(60,  'HUF');
      self::$IDR= new self(61,  'IDR');
      self::$ILS= new self(62,  'ILS');
      self::$IMP= new self(63,  'IMP');
      self::$INR= new self(64,  'INR');
      self::$IQD= new self(65,  'IQD');
      self::$IRR= new self(66,  'IRR');
      self::$ISK= new self(67,  'ISK');
      self::$JEP= new self(68,  'JEP');
      self::$JMD= new self(69,  'JMD');
      self::$JOD= new self(70,  'JOD');
      self::$JPY= new self(71,  'JPY');
      self::$KES= new self(72,  'KES');
      self::$KGS= new self(73,  'KGS');
      self::$KHR= new self(74,  'KHR');
      self::$KMF= new self(75,  'KMF');
      self::$KPW= new self(76,  'KPW');
      self::$KRW= new self(77,  'KRW');
      self::$KWD= new self(78,  'KWD');
      self::$KYD= new self(79,  'KYD');
      self::$KZT= new self(80,  'KZT');
      self::$LAK= new self(81,  'LAK');
      self::$LBP= new self(82,  'LBP');
      self::$LKR= new self(83,  'LKR');
      self::$LRD= new self(84,  'LRD');
      self::$LSL= new self(85,  'LSL');
      self::$LTL= new self(86,  'LTL');
      self::$LVL= new self(87,  'LVL');
      self::$LYD= new self(88,  'LYD');
      self::$MAD= new self(89,  'MAD');
      self::$MDL= new self(90,  'MDL');
      self::$MGA= new self(91,  'MGA');
      self::$MKD= new self(92,  'MKD');
      self::$MMK= new self(93,  'MMK');
      self::$MNT= new self(94,  'MNT');
      self::$MOP= new self(95,  'MOP');
      self::$MRO= new self(96,  'MRO');
      self::$MTL= new self(97,  'MTL');
      self::$MUR= new self(98,  'MUR');
      self::$MVR= new self(99,  'MVR');
      self::$MWK= new self(100, 'MWK');
      self::$MXN= new self(101, 'MXN');
      self::$MYR= new self(102, 'MYR');
      self::$MZN= new self(103, 'MZN');
      self::$NAD= new self(104, 'NAD');
      self::$NGN= new self(105, 'NGN');
      self::$NIO= new self(106, 'NIO');
      self::$NOK= new self(107, 'NOK');
      self::$NPR= new self(108, 'NPR');
      self::$NZD= new self(109, 'NZD');
      self::$OMR= new self(110, 'OMR');
      self::$PAB= new self(111, 'PAB');
      self::$PEN= new self(112, 'PEN');
      self::$PGK= new self(113, 'PGK');
      self::$PHP= new self(114, 'PHP');
      self::$PKR= new self(115, 'PKR');
      self::$PLN= new self(116, 'PLN');
      self::$PYG= new self(117, 'PYG');
      self::$QAR= new self(118, 'QAR');
      self::$RON= new self(119, 'RON');
      self::$RSD= new self(120, 'RSD');
      self::$RUB= new self(121, 'RUB');
      self::$RWF= new self(122, 'RWF');
      self::$SAR= new self(123, 'SAR');
      self::$SBD= new self(124, 'SBD');
      self::$SCR= new self(125, 'SCR');
      self::$SDG= new self(126, 'SDG');
      self::$SEK= new self(127, 'SEK');
      self::$SGD= new self(128, 'SGD');
      self::$SHP= new self(129, 'SHP');
      self::$SLL= new self(130, 'SLL');
      self::$SOS= new self(131, 'SOS');
      self::$SPL= new self(132, 'SPL');
      self::$SRD= new self(133, 'SRD');
      self::$STD= new self(134, 'STD');
      self::$SVC= new self(135, 'SVC');
      self::$SYP= new self(136, 'SYP');
      self::$SZL= new self(137, 'SZL');
      self::$THB= new self(138, 'THB');
      self::$TJS= new self(139, 'TJS');
      self::$TMM= new self(140, 'TMM');
      self::$TND= new self(141, 'TND');
      self::$TOP= new self(142, 'TOP');
      self::$TRY= new self(143, 'TRY');
      self::$TTD= new self(144, 'TTD');
      self::$TVD= new self(145, 'TVD');
      self::$TWD= new self(146, 'TWD');
      self::$TZS= new self(147, 'TZS');
      self::$UAH= new self(148, 'UAH');
      self::$UGX= new self(149, 'UGX');
      self::$USD= new self(150, 'USD');
      self::$UYU= new self(151, 'UYU');
      self::$UZS= new self(152, 'UZS');
      self::$VEB= new self(153, 'VEB');
      self::$VEF= new self(154, 'VEF');
      self::$VND= new self(155, 'VND');
      self::$VUV= new self(156, 'VUV');
      self::$WST= new self(157, 'WST');
      self::$XAF= new self(158, 'XAF');
      self::$XAG= new self(159, 'XAG');
      self::$XAU= new self(160, 'XAU');
      self::$XCD= new self(161, 'XCD');
      self::$XDR= new self(162, 'XDR');
      self::$XOF= new self(163, 'XOF');
      self::$XPD= new self(164, 'XPD');
      self::$XPF= new self(165, 'XPF');
      self::$XPT= new self(166, 'XPT');
      self::$YER= new self(167, 'YER');
      self::$ZAR= new self(168, 'ZAR');
      self::$ZMK= new self(169, 'ZMK');
      self::$ZWD= new self(170, 'ZWD');
    }

    /**
     * Returns all enum members
     *
     * @return  lang.Enum[]
     */
    public static function values() {
      return parent::membersOf(__CLASS__);
    }
    
    /**
     * Gets the currency instance for a given currency code
     *
     * @param   string code ISO 4217 code
     * @return  util.Currency
     * @throws  lang.IllegalArgumentException
     */
    public static function getInstance($code) {
      return Enum::valueOf(XPClass::forName(xp::nameOf(__CLASS__)), $code);
    }
  }
?>
