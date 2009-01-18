/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.remote;

/**
 * EASC Remoting
 *
 */
public class Remote {
    protected ProtocolHandler handler;
    
    /**
     * Constructor
     *
     */
    protected Remote(ProtocolHandler handler) {
        this.handler = handler;
    }

    /**
     * Acquire a connection for a given DSN
     *
     */
    protected static Remote acquire(DSN dsn) {
        if (-1 != dsn.getScheme().indexOf("+ssl")) {
            throw new IllegalArgumentException("SSL not supported");
        }
        
        // Get correct protocol handler (TODO: Use Factory)
        ProtocolHandler p= new XpProtocolHandler();
        p.initialize(dsn);
        return new Remote(p);
    }

    /**
     * Acquire a connection for a given DSN
     *
     */
    protected static Remote acquire(String dsn) {
        return acquire(new DSN(dsn));
    }
    
    /**
     * Retrieves a remote instance for a list of dsns specified by a
     * a list of strings.
     *
     */
    public static Remote forName(String... dsns) {
        try {
            return Try.these(Lists.collect(dsns, Functions.call(
                Remote.class, 
                Remote.class, 
                "acquire", 
                String.class
            )));
        } catch (Throwable e) {
            throw new RemoteException(e);
        }
    }

    /**
     * Retrieves a remote instance for a list of dsns specified by a
     * string. Multiple DSNs may be separated by commas.
     *
     */
    public static Remote forName(String dsns) throws IllegalArgumentException {
        if (null == dsns || "".equals(dsns)) {
            throw new IllegalArgumentException("Argument dsns may neither be null nor an empty string");
        }
        return forName(dsns.split(","));
    }

    /**
     * Looks up a remote object by its name
     *
     */
    public Object lookup(String name) throws IllegalArgumentException {
        if (null == name || "".equals(name)) {
            throw new IllegalArgumentException("Argument name may neither be null nor an empty string");
        }
        
        return this.handler.lookup(name); 
    }

    /**
     * Begins a transaction
     *
     */
    public Transaction begin(Transaction t) throws IllegalArgumentException {
        if (null == t) {
            throw new IllegalArgumentException("Argument t may neither be null");
        }
        
        return t.begin(this.handler);
    }
    
    /**
     * Creates a string representation of this Remote object
     *
     */
    @Override public String toString() {
        return "Remote(handler= " + this.handler + ")";
    }
}
