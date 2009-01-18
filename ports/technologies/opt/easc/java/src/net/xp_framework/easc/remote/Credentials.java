/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.remote;

public class Credentials {
    protected String username;
    protected String password;
    
    public Credentials(String username, String password) {
        this.username = username;
        this.password = password;
    }

    public Credentials(String username) {
        this(username, null);
    }
    
    public Credentials(String[] input) throws IllegalArgumentException {
        switch (input.length) {
            case 0: 
                this.username = null;
                this.password = null;
                break;
                
            case 1:
                this.username = input[0];
                this.password = null;
                break;

            case 2:
                this.username = input[0];
                this.password = input[1];
                break;

            default: 
                throw new IllegalArgumentException("Too many arguments");
        }
    }
    
    public String getUsername() {
        return this.username;
    }

    public String getPassword() {
        return this.password;
    }
    
    /**
     * Creates a string representation of this credentials object
     *
     */
    @Override public String toString() {
        return new StringBuilder()
            .append("Credentials(username= ")
            .append(this.username)
            .append(", password= ")
            .append(this.password)
            .append(")")
            .toString()
        ;   
    }
}
