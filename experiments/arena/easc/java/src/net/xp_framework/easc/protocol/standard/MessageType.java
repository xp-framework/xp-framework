/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.protocol.standard;

import java.util.HashMap;
import java.io.DataInputStream;
import java.io.IOException;
import java.lang.reflect.Method;
import net.xp_framework.easc.server.ServerContext;
import net.xp_framework.easc.server.Delegate;
import net.xp_framework.easc.server.LookupDelegate;
import net.xp_framework.easc.server.InitializationDelegate;
import net.xp_framework.easc.server.CallDelegate;
import net.xp_framework.easc.server.FinalizeDelegate;
import net.xp_framework.easc.server.TransactionDelegate;
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
        public Delegate delegateFrom(DataInputStream in, ServerContext ctx) throws IOException {
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
        public Delegate delegateFrom(DataInputStream in, ServerContext ctx) throws IOException {
            String jndiName= ByteCountedString.readFrom(in);
            return new LookupDelegate(jndiName);
        }
    }, 

    Call {
        public Delegate delegateFrom(DataInputStream in, ServerContext ctx) throws IOException {
            Long objectId= new Long(in.readLong());
            String methodName= ByteCountedString.readFrom(in);
            String serialized= ByteCountedString.readFrom(in);
            
            Object instance= ctx.objects.get(objectId.intValue());
            
            // Sanity check object
            if (null == instance) {
                throw new IOException("Cannot find object: " + objectId + " in server context");
            }
            
            // Deserialize arguments
            Object arguments[]= null;
            try {
                arguments= (Object[])Serializer.valueOf(serialized, instance.getClass().getClassLoader());
            } catch (Exception e) {
                e.printStackTrace();
                throw new IOException("Cannot deserialize arguments: " + e.getMessage());
            }
            
            // Find method
            Method method= methodFor(instance.getClass(), methodName, arguments);
            if (null != method) return new CallDelegate(instance, method, arguments);
            
            // Could not find method. Create verbose error message containing:
            //
            // * Interface name of first interface this proxy implements
            //   We can safely assume the class object is a proxy instance
            //   because only such instances get put into the ServerContext's
            //   object lookup map
            //
            // * The method name
            //
            // * The passed argument's classes names
            //
            // Examples:
            //   "Method net.xp_framework.unittest.ITest.nonExistant() not found"
            //   "
            //
            // This makes it easier to find type-related problems (e.g. calling 
            // a method declared as void setId(long id) { } with an int as
            // argument).
            StringBuffer s= new StringBuffer()
                .append("Method ")
                .append(instance.getClass().getInterfaces()[0].getName())
                .append('.')
                .append(methodName)
                .append('(');

            if (arguments.length > 0) {
                for (Object a: arguments) {
                    s.append(null == a ? "null" : a.getClass().getName()).append(", ");
                }
                s.delete(s.length() - 2, s.length());
            }
            s.append(") not found");
            throw new IOException(s.toString());
        }
    },

    Finalize {
        public Delegate delegateFrom(DataInputStream in, ServerContext ctx) throws IOException {
            return new FinalizeDelegate();
        }
    },

    Transaction {
        public Delegate delegateFrom(DataInputStream in, ServerContext ctx) throws IOException {
            int operation= in.readInt();
            return new TransactionDelegate(operation);
        }
    }, 
        
    Value {
        public Delegate delegateFrom(DataInputStream in, ServerContext ctx) throws IOException {
            return null;
        }
    },
    
    Exception {
        public Delegate delegateFrom(DataInputStream in, ServerContext ctx) throws IOException {
            return null;
        }
    },
    
    Error {
        public Delegate delegateFrom(DataInputStream in, ServerContext ctx) throws IOException {
            return null;
        }
    };
    
    // Set up ordinal -> enum instance mapping
    private static HashMap<Integer, MessageType> map= new HashMap<Integer, MessageType>(); 
    static {
        for (MessageType t : MessageType.values()) {
            map.put(t.ordinal(), t);
        }
    }

    abstract public Delegate delegateFrom(DataInputStream in, ServerContext ctx) throws IOException;
    
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
