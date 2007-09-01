<?php
/* This class is part of the XP framework
 *
 * $Id: P3PHeader.class.php 8975 2006-12-27 18:06:40Z friebe $ 
 */

  namespace peer::http;

  ::uses('peer.Header');

  define('P3PC_ACCESS_NONIDENT',              'NOI');
  define('P3PC_ACCESS_ALL',                   'ALL');
  define('P3PC_ACCESS_CONTACT_AND_OTHER',     'CAO');
  define('P3PC_ACCESS_IDENT_CONTACT',         'IDC');
  define('P3PC_ACCESS_OTHER_INDENT',          'OTI');
  define('P3PC_ACCESS_NONE',                  'NON');

  define('P3PC_REMEDIES_CORRECT',             'COR');
  define('P3PC_REMEDIES_MONEY',               'MON');
  define('P3PC_REMEDIES_LAW',                 'LAW');

  define('P3PC_CREQ_ALWAYS',                  'a');
  define('P3PC_CREQ_OPT_IN',                  'i');
  define('P3PC_CREQ_OPT_OUT',                 'o');

  define('P3PC_PURPOSE_CURRENT',              'CUR');
  define('P3PC_PURPOSE_ADMIN',                'ADM');
  define('P3PC_PURPOSE_DEVELOP',              'DEV');
  define('P3PC_PURPOSE_TAILORING',            'TAI');
  define('P3PC_PURPOSE_PSEUDO_ANALYSIS',      'PSA');
  define('P3PC_PURPOSE_PSEUDO_DECISION',      'PSD');
  define('P3PC_PURPOSE_INDIVIDUAL_ANALYSIS',  'IVA');
  define('P3PC_PURPOSE_INDIVIDUAL_DECISION',  'IVD');
  define('P3PC_PURPOSE_CONTACT',              'CON');
  define('P3PC_PURPOSE_HISTORICAL',           'HIS');
  define('P3PC_PURPOSE_TELEMARKETING',        'TEL');
  define('P3PC_PURPOSE_OTHER_PURPOSE',        'OTP');

  define('P3PC_RECIPIENT_OURS',               'OUR');
  define('P3PC_RECIPIENT_DELIVERY',           'DEL');
  define('P3PC_RECIPIENT_SAME',               'SAM');
  define('P3PC_RECIPIENT_UNRELATED',          'UNR');
  define('P3PC_RECIPIENT_PUBLIC',             'PUB');
  define('P3PC_RECIPIENT_OTHER_RECIPIENT',    'OTR');

  define('P3PC_RETENTION_NO_RETENTION',       'NOR');
  define('P3PC_RETENTION_STATED_PURPOSE',     'STP');
  define('P3PC_RETENTION_LEGAL_REQUIREMENT',  'LEG');
  define('P3PC_RETENTION_BUSINESS_PRACTICES', 'BUS');
  define('P3PC_RETENTION_INDEFINITELY',       'IND');

  define('P3PC_CATEGORIES_PHYSICAL',          'PHY');
  define('P3PC_CATEGORIES_ONLINE',            'ONL');
  define('P3PC_CATEGORIES_UNIQUEID',          'UNI');
  define('P3PC_CATEGORIES_PURCHASE',          'PUR');
  define('P3PC_CATEGORIES_FINANCIAL',         'FIN');
  define('P3PC_CATEGORIES_COMPUTER',          'COM');
  define('P3PC_CATEGORIES_NAVIGATION',        'NAV');
  define('P3PC_CATEGORIES_INTERACTIVE',       'INT');
  define('P3PC_CATEGORIES_DEMOGRAPHIC',       'DEM');
  define('P3PC_CATEGORIES_CONTENT',           'CNT');
  define('P3PC_CATEGORIES_STATE',             'STA');
  define('P3PC_CATEGORIES_POLITICAL',         'POL');
  define('P3PC_CATEGORIES_HEALTH',            'HEA');
  define('P3PC_CATEGORIES_PREFERENCE',        'PRE');
  define('P3PC_CATEGORIES_LOCATION',          'LOC');
  define('P3PC_CATEGORIES_GOVERNMENT',        'GOV');
  define('P3PC_CATEGORIES_OTHER_CATEGORY',    'OTC');

  /**
   * Represents a Private Preferences header.
   *
   * @see      http://www.w3.org/TR/P3P/#syntax_ext
   * @see      http://www.w3.org/TR/P3P/#compact_policies 
   * @purpose  P3P header
   */
  class P3PHeader extends peer::Header {
    public 
      $policyref = '',
      $compact   = array();

    /**
     * Constructor
     *
     * @param   string policyref
     */
    public function __construct($policyref) {
      parent::__construct('P3P', NULL);
      $this->policyref= $policyref;
    }
    
    /**
     * Helper method
     *
     * @param   string name
     * @param   string value
     */
    protected function _setCompact($name, $value) {
      if ($value) {
        $this->compact[$name]= $value;
      } else {
        unset($this->compact[$name]);
      }
    }
    
    /**
     * Set compact access. Information in the ACCESS element is 
     * represented in compact policies using tokens composed 
     * by a three letter code.
     *
     * @param   string access one of the P3PC_ACCESS_* constants
     */
    public function setCompactAccess($access) {
      $this->_setCompact('access', $access);
    }

    /**
     * Set compact disputes. If a full P3P policy contains a 
     * DISPUTES-GROUP element that contains one or more DISPUTES 
     * elements, then the server should signal the user agent by 
     * providing a single "DSP" token in the P3P-compact policy 
     * field.
     *
     * @param   bool disputes
     */
    public function setCompactDisputes($disputes) {
      $this->_setCompact('disputes', $disputes ? 'DSP' : NULL);
    }

    /**
     * Set compact remedies. Information in the REMEDIES element is 
     * represented in compact policies using tokens composed 
     * by a three letter code.
     *
     * Note: If NULL is passed as value for remedies, the REMEDIES
     * element will be removed
     *
     * @param   string remedies one of the P3PC_REMEDIES_* constants
     */
    public function setCompactRemedies($remedies) {
      $this->_setCompact('remedies', $remedies);
    }

    /**
     * Set compact non-identifiable. The presence of the NON-IDENTIFIABLE 
     * element in every statement of the policy is signaled by the NID 
     * token (note that the NID token MUST NOT be used unless the 
     * NON-IDENTIFIABLE element is present in every statement within the 
     * policy)
     *
     * @param   bool non_identifiable
     */
    public function setCompactNonIdentifiable($non_identifiable) {
      $this->_setCompact('non-identifiable', $non_identifiable ? 'NID' : NULL);
    }

    /**
     * Set compact purpose. Purposes are expressed in P3P compact policy 
     * format using tokens composed by a three letter code plus an 
     * optional one letter attribute. Such an optional attribute encodes 
     * the value of the "required" attribute in full P3P policies: 
     * its value can be "a", "i" and "o", which mean that the "required" 
     * attribute in the corresponding P3P policy must be set to "always",
     * "opt-in" and "opt-out" respectively.
     *
     * If a P3P compact policy needs to specify one or more other-purposes 
     * in its full P3P policy, a single OTP flag is used to signal the user 
     * agent that other-purposes exist in the full P3P policy.
     *
     * Note: If NULL is passed as value for purpose, the PURPOSE
     * element will be removed
     *
     * @param   string purpose one of the P3PC_PURPOSE_* constants
     * @param   string creq default '' one of the P3PC_CREQ_* constants
     */
    public function setCompactPurpose($purpose, $creq= '') {
      $this->_setCompact('purpose', $purpose.$creq);
    }
    
    /**
     * Set compact recipient. Recipients are expressed in P3P compact 
     * policy format using a three letter code plus an optional one 
     * letter attribute. Such an optional attribute encodes the value 
     * of the "required" attribute in full P3P policies: its value can 
     * be "a", "i" and "o", which mean that the "required" attribute 
     * in the corresponding P3P policy must be set to "always", 
     * "opt-in" and "opt-out" respectively.
     *
     * Note: If NULL is passed as value for recipient, the RECIPIENT
     * element will be removed
     *
     * @param   string recipient one of the P3PC_RECIPIENT_* constants
     * @param   string creq default '' one of the P3PC_CREQ_* constants
     */
    public function setCompactRecipient($recipient, $creq= '') {
      $this->_setCompact('recipient', $recipient.$creq);
    }

    /**
     * Set compact retention. Information in the RETENTION element 
     * is represented in compact policies composed 
     * by a three letter code.
     *
     * Note: If NULL is passed as value for retention, the RETENTION
     * element will be removed
     *
     * @param   string retention one of the P3PC_RETENTION_* constants
     */
    public function setCompactRetention($retention) {
      $this->_setCompact('retention', $retention);
    }

    /**
     * Set compact categories. Categories are represented in compact 
     * policies by a three letter code.
     *
     * Note: If NULL is passed as value for categories, the CATEGORIES
     * element will be removed
     *
     * @param   string categories one of the P3PC_CATEGORIES_* constants
     */
    public function setCompactCategories($categories) {
      $this->_setCompact('categories', $categories);
    }

    /**
     * Set compact test. The presence of the TEST element is signaled 
     * by the TST token.
     *
     * @param   bool test
     */
    public function setCompactTest($test) {
      $this->_setCompact('test', $test ? 'TST' : NULL);
    }

    /**
     * Set Policyref
     *
     * @param   string policyref
     */
    public function setPolicyref($policyref) {
      $this->policyref= $policyref;
    }

    /**
     * Get Policyref
     *
     * @return  string
     */
    public function getPolicyref() {
      return $this->policyref;
    }
    
    /**
     * Get header value representation
     *
     * @return  string value
     */
    public function getValueRepresentation() {
      $r= 'policyref="'.$this->policyref.'"';
      if ($this->compact) {                     // Compact policy is optional
        $r.= ', CP="'.implode(' ', array_values($this->compact)).'"';
      }
      return $r;
    }
  }
?>
