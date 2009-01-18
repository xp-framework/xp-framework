/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.remote;

/**
 * EASC Remoting: Protocol handler
 *
 */
public interface ProtocolHandler {

    /**
     * Initializes this protocol
     *
     */
    void initialize(DSN dsn);
    
    /**
     * Performs a lookup
     *
     */
    Object lookup(String name);

    /**
     * Invokes a method
     *
     */
    Object invoke(long oid, String method, Object[] args);

    /**
     * Begins a transaction
     *
     */
    Transaction begin(Transaction t);

    /**
     * Commits a transaction
     *
     */
    void commit(Transaction t);

    /**
     * Rolls back a transaction
     *
     */
    void rollback(Transaction t);
}
