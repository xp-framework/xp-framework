<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Indicate an LDAP error
   *
   * @purpose  Exception 
   * @ext      ldap
   * @see      http://developer.netscape.com/docs/manuals/dirsdk/jsdk40/Reference/netscape/ldap/LDAPException.html
   */
  class LDAPException extends XPException {
    const
      LDAP_SUCCESS = 0x0000,
      LDAP_OPERATIONS_ERROR = 0x0001,
      LDAP_PROTOCOL_ERROR = 0x0002,
      LDAP_TIMELIMIT_EXCEEDED = 0x0003,
      LDAP_SIZELIMIT_EXCEEDED = 0x0004,
      LDAP_COMPARE_FALSE = 0x0005,
      LDAP_COMPARE_TRUE = 0x0006,
      LDAP_AUTH_METHOD_NOT_SUPPORTED = 0x0007,
      LDAP_STRONG_AUTH_REQUIRED = 0x0008,
      LDAP_PARTIAL_RESULTS = 0x0009,
      LDAP_REFERRAL = 0x000A,
      LDAP_ADMINLIMIT_EXCEEDED = 0x000B,
      LDAP_UNAVAILABLE_CRITICAL_EXTENSION = 0x000C,
      LDAP_CONFIDENTIALITY_REQUIRED = 0x000D,
      LDAP_SASL_BIND_INPROGRESS = 0x000E,
      LDAP_NO_SUCH_ATTRIBUTE = 0x0010,
      LDAP_UNDEFINED_TYPE = 0x0011,
      LDAP_INAPPROPRIATE_MATCHING = 0x0012,
      LDAP_CONSTRAINT_VIOLATION = 0x0013,
      LDAP_TYPE_OR_VALUE_EXISTS = 0x0014,
      LDAP_INVALID_SYNTAX = 0x0015,
      LDAP_NO_SUCH_OBJECT = 0x0020,
      LDAP_ALIAS_PROBLEM = 0x0021,
      LDAP_INVALID_DN_SYNTAX = 0x0022,
      LDAP_IS_LEAF = 0x0023,
      LDAP_ALIAS_DEREF_PROBLEM = 0x0024,
      LDAP_INAPPROPRIATE_AUTH = 0x0030,
      LDAP_INVALID_CREDENTIALS = 0x0031,
      LDAP_INSUFFICIENT_ACCESS = 0x0032,
      LDAP_BUSY = 0x0033,
      LDAP_UNAVAILABLE = 0x0034,
      LDAP_UNWILLING_TO_PERFORM = 0x0035,
      LDAP_LOOP_DETECT = 0x0036,
      LDAP_SORT_CONTROL_MISSING = 0x003C,
      LDAP_INDEX_RANGE_ERROR = 0x003D,
      LDAP_NAMING_VIOLATION = 0x0040,
      LDAP_OBJECT_CLASS_VIOLATION = 0x0041,
      LDAP_NOT_ALLOWED_ON_NONLEAF = 0x0042,
      LDAP_NOT_ALLOWED_ON_RDN = 0x0043,
      LDAP_ALREADY_EXISTS = 0x0044,
      LDAP_NO_OBJECT_CLASS_MODS = 0x0045,
      LDAP_RESULTS_TOO_LARGE = 0x0046,
      LDAP_AFFECTS_MULTIPLE_DSAS = 0x0047,
      LDAP_OTHER = 0x0050,
      LDAP_SERVER_DOWN = 0x0051,
      LDAP_LOCAL_ERROR = 0x0052,
      LDAP_ENCODING_ERROR = 0x0053,
      LDAP_DECODING_ERROR = 0x0054,
      LDAP_TIMEOUT = 0x0055,
      LDAP_AUTH_UNKNOWN = 0x0056,
      LDAP_FILTER_ERROR = 0x0057,
      LDAP_USER_CANCELLED = 0x0058,
      LDAP_PARAM_ERROR = 0x0059,
      LDAP_NO_MEMORY = 0x005A,
      LDAP_CONNECT_ERROR = 0x005B,
      LDAP_NOT_SUPPORTED = 0x005C,
      LDAP_CONTROL_NOT_FOUND = 0x005D,
      LDAP_NO_RESULTS_RETURNED = 0x005E,
      LDAP_MORE_RESULTS_TO_RETURN = 0x005F,
      LDAP_CLIENT_LOOP = 0x0060,
      LDAP_REFERRAL_LIMIT_EXCEEDED = 0x0061;

    public
      $code = 0;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string message
     * @param   int code
     */
    public function __construct($message, $code) {
      $this->code= $code;
      parent::__construct($message);
    }

    /**
     * Get Code
     *
     * @access  public
     * @return  int
     */
    public function getCode() {
      return $this->code;
    }
    
    /**
     * Create string representation
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      return parent::toString().sprintf(
        "  *** LDAP code #%d [%s]\n",
        $this->code,
        ldap_err2str($this->code)
      );
    }
  }
?>
