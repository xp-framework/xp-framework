<?php
/* This class is part of the XP framework
 *
 * $Id: LDAPException.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace peer::ldap;

  define('LDAP_SUCCESS',                         0x0000);
  define('LDAP_OPERATIONS_ERROR',                0x0001);
  define('LDAP_PROTOCOL_ERROR',                  0x0002);
  define('LDAP_TIMELIMIT_EXCEEDED',              0x0003);
  define('LDAP_SIZELIMIT_EXCEEDED',              0x0004);
  define('LDAP_COMPARE_FALSE',                   0x0005);
  define('LDAP_COMPARE_TRUE',                    0x0006);
  define('LDAP_AUTH_METHOD_NOT_SUPPORTED',       0x0007);
  define('LDAP_STRONG_AUTH_REQUIRED',            0x0008);
  define('LDAP_PARTIAL_RESULTS',                 0x0009);
  define('LDAP_REFERRAL',                        0x000A);
  define('LDAP_ADMINLIMIT_EXCEEDED',             0x000B);
  define('LDAP_UNAVAILABLE_CRITICAL_EXTENSION',  0x000C);
  define('LDAP_CONFIDENTIALITY_REQUIRED',        0x000D);
  define('LDAP_SASL_BIND_INPROGRESS',            0x000E);
  define('LDAP_NO_SUCH_ATTRIBUTE',               0x0010);
  define('LDAP_UNDEFINED_TYPE',                  0x0011);
  define('LDAP_INAPPROPRIATE_MATCHING',          0x0012);
  define('LDAP_CONSTRAINT_VIOLATION',            0x0013);
  define('LDAP_TYPE_OR_VALUE_EXISTS',            0x0014);
  define('LDAP_INVALID_SYNTAX',                  0x0015);
  define('LDAP_NO_SUCH_OBJECT',                  0x0020);
  define('LDAP_ALIAS_PROBLEM',                   0x0021);
  define('LDAP_INVALID_DN_SYNTAX',               0x0022);
  define('LDAP_IS_LEAF',                         0x0023);
  define('LDAP_ALIAS_DEREF_PROBLEM',             0x0024);
  define('LDAP_INAPPROPRIATE_AUTH',              0x0030);
  define('LDAP_INVALID_CREDENTIALS',             0x0031);
  define('LDAP_INSUFFICIENT_ACCESS',             0x0032);
  define('LDAP_BUSY',                            0x0033);
  define('LDAP_UNAVAILABLE',                     0x0034);
  define('LDAP_UNWILLING_TO_PERFORM',            0x0035);
  define('LDAP_LOOP_DETECT',                     0x0036);
  define('LDAP_SORT_CONTROL_MISSING',            0x003C);
  define('LDAP_INDEX_RANGE_ERROR',               0x003D);
  define('LDAP_NAMING_VIOLATION',                0x0040);
  define('LDAP_OBJECT_CLASS_VIOLATION',          0x0041);
  define('LDAP_NOT_ALLOWED_ON_NONLEAF',          0x0042);
  define('LDAP_NOT_ALLOWED_ON_RDN',              0x0043);
  define('LDAP_ALREADY_EXISTS',                  0x0044);
  define('LDAP_NO_OBJECT_CLASS_MODS',            0x0045);
  define('LDAP_RESULTS_TOO_LARGE',               0x0046);
  define('LDAP_AFFECTS_MULTIPLE_DSAS',           0x0047);
  define('LDAP_OTHER',                           0x0050);
  define('LDAP_SERVER_DOWN',                     0x0051);
  define('LDAP_LOCAL_ERROR',                     0x0052);
  define('LDAP_ENCODING_ERROR',                  0x0053);
  define('LDAP_DECODING_ERROR',                  0x0054);
  define('LDAP_TIMEOUT',                         0x0055);
  define('LDAP_AUTH_UNKNOWN',                    0x0056);
  define('LDAP_FILTER_ERROR',                    0x0057);
  define('LDAP_USER_CANCELLED',                  0x0058);
  define('LDAP_PARAM_ERROR',                     0x0059);
  define('LDAP_NO_MEMORY',                       0x005A);
  define('LDAP_CONNECT_ERROR',                   0x005B);
  define('LDAP_NOT_SUPPORTED',                   0x005C);
  define('LDAP_CONTROL_NOT_FOUND',               0x005D);
  define('LDAP_NO_RESULTS_RETURNED',             0x005E);
  define('LDAP_MORE_RESULTS_TO_RETURN',          0x005F);
  define('LDAP_CLIENT_LOOP',                     0x0060);
  define('LDAP_REFERRAL_LIMIT_EXCEEDED',         0x0061);

  /**
   * Indicate an LDAP error
   *
   * @purpose  Exception 
   * @ext      ldap
   * @see      http://developer.netscape.com/docs/manuals/dirsdk/jsdk40/Reference/netscape/ldap/LDAPException.html
   */
  class LDAPException extends lang::XPException {
    public
      $errorcode = 0;
      
    /**
     * Constructor
     *
     * @param   string message
     * @param   int errorcode
     */
    public function __construct($message, $errorcode) {
      parent::__construct($message);
      $this->errorcode= $errorcode;
    }

    /**
     * Get errorcode
     *
     * @return  int
     */
    public function getErrorCode() {
      return $this->errorcode;
    }

    /**
     * Return compound message of this exception.
     *
     * @return  string
     */
    public function compoundMessage() {
      return sprintf(
        'Exception %s (LDAP errorcode #%d [%s]: %s)',
        $this->getClassName(),
        $this->errorcode,
        ldap_err2str($this->errorcode),
        $this->message
      );
    }
  }
?>
