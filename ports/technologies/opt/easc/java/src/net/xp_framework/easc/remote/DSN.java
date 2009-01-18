/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.remote;

import java.util.regex.Pattern;
import java.util.regex.Matcher;
import java.util.HashMap;

public class DSN {
    private static final Pattern DSN_PATTERN = Pattern.compile("([a-z+]+)://(([^@]+)@)?([^:?]+)(:([0-9]+))?(\\?(.+))?");
    
    protected String scheme;
    protected String host;
    
    protected Credentials credentials = null;
    protected int port = -1;
    protected HashMap<String, String> params = new HashMap<String, String>();

    /**
     * Constructor
     *
     */
    public DSN(String in) throws IllegalArgumentException {
        String group;

        Matcher m= DSN_PATTERN.matcher(in);
        if (!m.matches()) {
            throw new IllegalArgumentException("Malformed DSN '" + in + "'");
        }
        this.scheme= m.group(1);
        this.host= m.group(4);
        
        // Parse credentials
        group = m.group(3);
        if (null != group) {
            this.credentials= new Credentials(group.split(":"));
        }
        
        // Parse port
        group = m.group(6);
        if (null != group) {
            this.port = Integer.parseInt(group);
        }
        
        // Parse parameters
        group = m.group(8);
        if (null != group) {
            for (String param : m.group(8).split("&")) {
                int pos= param.indexOf('=');
                params.put(param.substring(0, pos), param.substring(pos+ 1, param.length()));                
            }
        }
    }
    
    /**
     * Gets this DSN's scheme
     *
     */
    public String getScheme() {
        return this.scheme;
    }

    /**
     * Gets this DSN's host
     *
     */
    public String getHost() {
        return this.host;
    }

    /**
     * Gets this DSN's port
     *
     */
    public int getPort() {
        return this.port;
    }

    /**
     * Gets this DSN's port or a specified default port if no port was set
     *
     */
    public int getPort(int defaultPort) {
        return -1 == this.port ? defaultPort : this.port;
    }

    /**
     * Gets whether this DSN has credentials set
     *
     */
    public boolean hasCredentials() {
        return null != this.credentials;
    }

    /**
     * Gets this DSN's credentials
     *
     */
    public Credentials getCredentials() {
        return this.credentials;
    }

    /**
     * Gets this DSN's parameters
     *
     */
    public HashMap<String, String> getParameters() {
        return this.params;
    }

    /**
     * Gets whether this DSN has a parameters by the given name
     *
     */
    public boolean hasParameter(String name) {
        return this.params.containsKey(name);
    }

    /**
     * Gets one of this DSN's parameters by the given name
     *
     */
    public String getParameter(String name) {
        return this.params.get(name);
    }

    /**
     * Gets one of this DSN's parameters by name or a specified default value if no 
     * such parameter exists.
     *
     */
    public String getParameter(String name, String defaultValue) {
        return this.params.containsKey(name) ? this.params.get(name) : defaultValue;
    }

    /**
     * Creates a string representation of this DSN object
     *
     */
    @Override public String toString() {
        return new StringBuilder()
            .append("DSN<")
            .append(this.scheme)
            .append("://")
            .append(this.host)
            .append(":")
            .append(this.port == -1 ? "default" : String.valueOf(this.port))
            .append("> {\n  credentials = ")
            .append(this.credentials)
            .append("\n  parameters  = ")
            .append(this.params)
            .append("\n}>")
            .toString()
        ;
    }
}
