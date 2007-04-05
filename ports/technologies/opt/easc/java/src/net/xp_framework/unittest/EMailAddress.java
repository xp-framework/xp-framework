/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.unittest;

/**
 * EMailAddress class, used for readResolve() testing
 *
 * @purpose Value object for SerializerTest
 */
public class EMailAddress {
    protected String data;
    private final transient String localpart;
    private final transient String domain;
    
    /**
     * Public no-arg constructor. Sets localpart and domain to NULL.
     *
     */
    public EMailAddress() {
        this.localpart= null;
        this.domain= null;
    }
    
    /**
     * Public constructor with localpart and domain.
     *
     * @access public
     * @param  java.lang.String localpart The local part of the mail address
     * @param  java.lang.String domain The domain part of the mail address
     */
    public EMailAddress(String localpart, String domain) {
        this.localpart= localpart;
        this.domain= domain;
    }
    
    /**
     * Implement readResolve() for serialization testing.
     *
     * @access protected
     * @throws java.io.ObjectStreamException
     * @return java.lang.Object
     */
    protected Object readResolve() throws java.io.ObjectStreamException {
        int pos= this.data.indexOf('@');
        return new EMailAddress(
            this.data.substring(0, pos), 
            this.data.substring(pos+ 1, this.data.length())
        );
    }

    /**
     * Override equals() which compares the localpart and the domain of
     * of the email address object.
     *
     * @access public
     * @param net.xp_framework.unittest.EMailAddress other The email address to compare
     * @return boolean
     */
    @Override public boolean equals(Object other) {
        if (!(other instanceof EMailAddress)) return false;
        
        EMailAddress mail= (EMailAddress)other;
        return (
            (null == this.localpart ? null == mail.localpart : this.localpart.equals(mail.localpart)) &&
            (null == this.domain ? null == mail.domain : this.domain.equals(mail.domain))
        );
    }
    
    /**
     * Override toString() to return readable string representation for
     * the email address.
     *
     * @access public
     * @return java.lang.String
     */
    @Override public String toString() {
        return "<" + this.localpart + "@" + this.domain + ">";
    }
}
