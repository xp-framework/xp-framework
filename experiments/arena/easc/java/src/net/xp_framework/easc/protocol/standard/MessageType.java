/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.protocol.standard;

import java.util.HashMap;
import java.io.DataInputStream;
import java.io.IOException;
import java.lang.reflect.Method;
import net.xp_framework.easc.server.ProxyMap;
import net.xp_framework.easc.server.Delegate;
import net.xp_framework.easc.server.LookupDelegate;
import net.xp_framework.easc.server.InitializationDelegate;
import net.xp_framework.easc.server.CallDelegate;
import net.xp_framework.easc.server.FinalizeDelegate;
import net.xp_framework.easc.util.ByteCountedString;

import static net.xp_framework.easc.util.MethodMatcher.methodFor;

/**
 * Message type enumeration
 * 
 * A) Request message types
 * ~~~~~~~~~~~~~~~~~~~~~~~~
 * 
 * Initialize
 * ==========
 * - Request
 *   This is the first message the client sends on the socket after connecting
 *   to the EASC server. It contains a boolean indicating whether authorization
 *   information is passed (and if so, a string containing the username and one
 *   containing the password).
 *
 * Lookup
 * ======
 * - Request
 *   The client sends this message for JNDI lookups. The message will contain 
 *   the JNDI name as its only contents.
 *
 * - Success response
 *   The server responds with a serialized proxy object.
 * 
 * Call
 * ====
 * - Request
 *   The client sends this message for remote method calls. It contains the 
 *   object identifier, the method name as a string and the arguments as an
 *   array.
 *
 * - Success response
 *   The server responds with the serialized representation of the return value
 *
 * - Exception response
 *   In case the method invocation caused an exception, the exception and its
 *   stack trace will be serialized
 *
 * - Method not found
 *   In case the specified method cannot be found, the server responds with a
 *   serialized NoSuchMethodException.
 *
 * Finalize
 * ========
 * - Request
 *   The client sends this message to gracefully disconnect
 *
 * - Response
 *   The server closes the socket
 *
 * B) Response message types
 * ~~~~~~~~~~~~~~~~~~~~~~~~~
 *
 * Value
 * =====
 * This message contains a serialized representation of any value.
 *
 * Exception
 * =========
 * This message contains a serialized java.lang.Exception or subclass
 *
 * Error
 * =====
 * This message contains a serialized java.lang.Error or subclass. After this
 * message is sent to the client the server closes the socket.
 *
 */
public enum MessageType {

    Initialize {
        public Delegate delegateFrom(DataInputStream in, ProxyMap map) throws IOException {
            if (in.readBoolean()) {
                return new InitializationDelegate(
                    ByteCountedString.readFrom(in),   // username
                    ByteCountedString.readFrom(in)    // password

                );
            }
            return new InitializationDelegate();
        }
    }, 
    
    Lookup {
        public Delegate delegateFrom(DataInputStream in, ProxyMap map) throws IOException {
            String jndiName= ByteCountedString.readFrom(in);
            return new LookupDelegate(jndiName);
        }
    }, 
    
    Call {
        public Delegate delegateFrom(DataInputStream in, ProxyMap map) throws IOException {
            long objectId= in.readLong();
            String methodName= ByteCountedString.readFrom(in);
            String serialized= ByteCountedString.readFrom(in);
            
            Object instance= map.getObject(objectId);
            Object arguments[]= null;
            try {
                arguments= (Object[])Serializer.valueOf(serialized, instance.getClass().getClassLoader());
            } catch (Exception e) {
                e.printStackTrace();
                throw new IOException("Cannot deserialize arguments: " + e.getMessage());
            }
            Method method= methodFor(instance.getClass(), methodName, arguments);
            if (null == method) {
                throw new IOException("Method '" + methodName + "' not found");
            }

            return new CallDelegate(instance, method, arguments);
        }
    },

    Finalize {
        public Delegate delegateFrom(DataInputStream in, ProxyMap map) throws IOException {
            return new FinalizeDelegate();
        }
    },
    
    Value {
        public Delegate delegateFrom(DataInputStream in, ProxyMap map) throws IOException {
            return null;
        }
    },
    
    Exception {
        public Delegate delegateFrom(DataInputStream in, ProxyMap map) throws IOException {
            return null;
        }
    },
    
    Error {
        public Delegate delegateFrom(DataInputStream in, ProxyMap map) throws IOException {
            return null;
        }
    };
    

    private static HashMap<Integer, MessageType> map= new HashMap<Integer, MessageType>(); 

    static {
        for (MessageType t : MessageType.values()) {
            map.put(t.ordinal(), t);
        }
    }

    abstract public Delegate delegateFrom(DataInputStream in, ProxyMap map) throws IOException;
    
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
