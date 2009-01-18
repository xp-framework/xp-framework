/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.remote;

/**
 * Indicates an exception occured in the remote API.
 *
 */
public class RemoteException extends RuntimeException {

    public RemoteException(Throwable e) {
        super(e);
    }

    public RemoteException(String message, Throwable e) {
        super(message, e);
    }

    public RemoteException(String message) {
        super(message);
    }
}
