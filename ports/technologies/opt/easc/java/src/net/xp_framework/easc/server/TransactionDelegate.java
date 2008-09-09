/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.server;

import net.xp_framework.easc.server.Delegate;
import java.lang.reflect.Proxy;
import javax.naming.InitialContext;
import javax.transaction.UserTransaction;
import net.xp_framework.easc.server.ServerContext;

/**
 * Handles transactions
 *
 * <ul>
 *   <li>1 - Begin a transaction</li>
 *   <li>2 - Retrieve transaction state</li>
 *   <li>3 - Commit transaction</li>
 *   <li>4 - Rollback transaction</li>
 * </ul>
 *
 * @see   net.xp_framework.easc.server.Delegate
 */
public class TransactionDelegate implements Delegate {
    int operation;

    public TransactionDelegate(int operation) {
        this.operation= operation;
    }

    public Object invoke(ServerContext ctx) throws Exception {
        switch (this.operation) {
            case 1: {
                ctx.transaction= (UserTransaction)((new InitialContext()).lookup("UserTransaction"));
                // DEBUG System.out.println("Beginning " + ctx.transaction);
                ctx.transaction.begin();
                break;
            }
            case 2: {
                // TBI
                break;
            }
            case 3: {
                // DEBUG System.out.println("Committing " + ctx.transaction);
                ctx.transaction.commit();
                break;
            }
            case 4: {
                // DEBUG System.out.println("Rolling back " + ctx.transaction);
                ctx.transaction.rollback();
                break;
            }
            default: {
                throw new IllegalArgumentException("Unknown operation " + this.operation);
            }
        }
        
        return true;
    }
    
    /**
     * Return a classloader to be used instead of the current one
     *
     */
    public ClassLoader getClassLoader() {
        return null;
    }
}
