<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * A user is basically the representation of the user agent
   * currently visiting your website. Users can be "tracked"
   * using cookies or sessions.
   *
   * We use sessions and Apache's mod_rewrite to simulate users
   * on a website. The user agent (the browser) transmits the 
   * session id on every request, so we assume it's the same user.
   *
   * A user is "impersonated" as soon as the user logs on. From
   * then on, we know more about the user, his or her name, 
   * permissions, preferences, etcetera. The person member variable
   * is set from outside (e.g., from a LoginHandler) and, as soon
   * as this is the case, the user is logged on. To retrieve this
   * information, you can use the <tt>isLoggedOn()</tt> method of this 
   * class.
   *
   * The language of a user might be determined by using the 
   * <tt>LocaleNegotiator</tt> class (which checks for the
   * HTTP_ACCEPT_LANGUAGE env var) or by asking the user to
   * select one. You might even want to use an IP2Country service
   * (which, of course, does not give you the _language_ spoken
   * by the visitor, but might be a good guess).
   *
   * Some notes on security:
   *
   * Of course, users can take over sessions by copying the URL
   * and sending them to other users, then recognized by our system
   * as the same user. We could try to prevent this by using cookies,
   * storing the session id in them and backchecking on every 
   * request if the session's id is still equal to the one in the
   * cookie. Still, a user might deny cookies or even create a 
   * cookie. For highly critical applications, the use of SSL is
   * suggested as we have a direct client-to-client communication 
   * here.
   *
   * Using the remote IP address is generally a bad idea. It
   * might be the same for two people (proxies) or change (some 
   * dial-up providers do this).
   *
   * @see      xp://org.apache.LocaleNegotiator
   * @purpose  Base class
   */
  class User extends Object {
    public
      $person        = NULL,
      $language      = '',
      $remoteAddress = '',
      $remoteHost    = '',
      $agentString   = '';
     
    /**
     * Constructor
     *
     * @access  public
     * @param   string remoteAddress
     * @param   string agentString
     * @param   string language default 'C'
     */ 
    public function __construct($remoteAddress, $agentString, $language= 'C') {
      $this->remoteAddress= $remoteAddress;
      $this->agentString= $agentString;
      $this->language= $language;
      
    }

    /**
     * Get RemoteAddress
     *
     * @access  public
     * @return  string
     */
    public function getRemoteAddress() {
      return $this->remoteAddress;
    }

    /**
     * Get Remote host (resolved name of remote IP). Note: The return
     * value of this function is cached!
     *
     * @access  public
     * @return  string
     */
    public function getRemoteHost() {
      if (empty($this->remoteHost)) {
        $this->remoteHost= gethostbyaddr($this->remoteAddress);
      }
      return $this->remoteHost;
    }

    /**
     * Get AgentString
     *
     * @access  public
     * @return  string
     */
    public function getAgentString() {
      return $this->agentString;
    }

    /**
     * Set Language
     *
     * @access  public
     * @param   string language
     */
    public function setLanguage($language) {
      $this->language= $language;
    }

    /**
     * Get Language
     *
     * @access  public
     * @return  string
     */
    public function getLanguage() {
      return $this->language;
    }

    /**
     * Set Person
     *
     * @access  public
     * @param   &org.apache.xml.workflow.AbstractPerson person
     */
    public function setPerson(&$person) {
      $this->person= $person;
    }

    /**
     * Get Person
     *
     * @access  public
     * @return  &org.apache.xml.workflow.AbstractPerson
     */
    public function getPerson() {
      return $this->person;
    }
    
    /**
     * Returns whether a user is logged on, which is when the
     * member variable "person" is not NULL.
     *
     * @access  public
     * @return  bool
     */
    public function isLoggedOn() {
      return (NULL !== $this->person);
    }
  }
?>
