/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.protocol.standard;

import java.util.HashMap;
import java.io.DataInputStream;
import java.io.IOException;

import net.xp_framework.easc.server.Delegate;
import net.xp_framework.easc.server.LookupDelegate;
import net.xp_framework.easc.server.InitializationDelegate;
import net.xp_framework.easc.server.StatusDelegate;
import net.xp_framework.easc.server.CallDelegate;
import net.xp_framework.easc.server.ValueDelegate;
import net.xp_framework.easc.server.FinalizeDelegate;

/**
 * Message type enumeration
 *
 */
public enum MessageType {
    Initialize {
        public Delegate delegateFrom(DataInputStream in) throws IOException {
            return new InitializationDelegate();
        }
    }, 
    
    Status {
        public Delegate delegateFrom(DataInputStream in) throws IOException {
            return new StatusDelegate();
        }
    }, 
    
    Lookup {
        public Delegate delegateFrom(DataInputStream in) throws IOException {
            String jndiName= in.readUTF();
            return new LookupDelegate(jndiName);
        }
    }, 
    
    Call {
        public Delegate delegateFrom(DataInputStream in) throws IOException {
            return new CallDelegate();
        }
    }, 
    
    Value {
        public Delegate delegateFrom(DataInputStream in) throws IOException {
            return new ValueDelegate();
        }
    }, 
    
    Finalize {
        public Delegate delegateFrom(DataInputStream in) throws IOException {
            return new FinalizeDelegate();
        }
    };

    private static HashMap<Integer, MessageType> map= new HashMap<Integer, MessageType>(); 

    static {
        for (MessageType t : MessageType.values()) {
            map.put(t.ordinal(), t);
        }
    }

    abstract public Delegate delegateFrom(DataInputStream in) throws IOException;
    
    /**
     * Get a type for a given message type identifier
     *
     * @static
     * @access  public
     * @param   int identifier the integer number passed on the wire
     * @return  net.xp_framework.easc.protocol.standard.MessageType
     */
    public static MessageType valueOf(int identifier) {
        return map.get(identifier);
    }
}
