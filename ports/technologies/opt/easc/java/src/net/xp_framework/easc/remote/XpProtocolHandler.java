/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.remote;

import net.xp_framework.easc.protocol.standard.Serializer;
import net.xp_framework.easc.protocol.standard.SerializerContext;
import net.xp_framework.easc.protocol.standard.SerializationException;
import net.xp_framework.easc.protocol.standard.Header;
import net.xp_framework.easc.protocol.standard.MessageType;
import net.xp_framework.easc.protocol.standard.TokenHandler;
import net.xp_framework.easc.protocol.standard.Length;
import net.xp_framework.easc.util.ByteCountedString;
import java.net.Socket;
import java.io.ByteArrayOutputStream;
import java.io.DataInputStream;
import java.io.DataOutputStream;
import java.io.IOException;
import java.lang.reflect.Proxy;

/**
 * EASC Remoting: Protocol handler
 *
 */
public class XpProtocolHandler implements ProtocolHandler {
    protected Socket sock;
    protected DataInputStream in;
    protected DataOutputStream out;
    
    static {
        // I:1:{s:21:"beans.test.TestRunner";}
        Serializer.tokenMap.put('I', new TokenHandler() {
            public Object handle(String serialized, Length length, SerializerContext context, Class clazz) throws Exception { 
                String oid = serialized.substring(2, serialized.indexOf(':', 2));
                String className = (String)Serializer.valueOf(
                    serialized.substring(oid.length() + 2 + 2),
                    length,
                    context,
                    clazz
                );

                Class c = null;
                try {
                    c = context.classLoader.loadClass(className); 
                } catch (ClassNotFoundException e) {
                    throw new SerializationException(context.classLoader + ": " + e.getMessage());
                }
                length.value += 2 + 2 + 1;    // "I:" + ":{" + "}"
                
                return Proxy.newProxyInstance(
                    context.classLoader, 
                    new Class[] { c }, 
                    new RemoteInvocationHandler((ProtocolHandler)context.handler, oid)
                );
            }
        });
    }

    /**
     * Initializes this protocol
     *
     */
    public void initialize(DSN dsn) {
        try {
            this.sock = new Socket(dsn.getHost(), dsn.getPort(6448));
            this.sock.setTcpNoDelay(true);
            
            this.in = new DataInputStream(this.sock.getInputStream());
            this.out = new DataOutputStream(this.sock.getOutputStream());
        } catch (IOException e) {
            throw new RemoteException(e);
        }
        
        if (dsn.hasCredentials()) {
            this.comm(MessageType.Initialize, "\1", 
                new ByteCountedString(dsn.getCredentials().getUsername()),
                new ByteCountedString(dsn.getCredentials().getPassword())
            );
        } else {
            this.comm(MessageType.Initialize, "\0");
        }
    }
    
    /**
     * Performs a lookup
     *
     */
    public Object lookup(String name) {
        return this.comm(MessageType.Lookup, "", new ByteCountedString(name));
    }

    /**
     * Invokes a method
     *
     */
    public Object invoke(long oid, String method, Object[] args) {
        ByteArrayOutputStream oidBytes= new ByteArrayOutputStream();
        try {
            new DataOutputStream(oidBytes).writeLong(oid);
        } catch (IOException never) { 
            // Ignore
        }
        
        try {
            return this.comm(
                MessageType.Call, 
                oidBytes.toString(), 
                new ByteCountedString(method), 
                new ByteCountedString(Serializer.representationOf(args))
            );
        } catch (Exception e) {             // Serializer.representationOf declares this in throws
            throw new RemoteException(e);
        }
    }

    /**
     * Begins a transaction
     *
     */
    public Transaction begin(Transaction t) {
        return null;
    }

    /**
     * Commits a transaction
     *
     */
    public void commit(Transaction t) {
    }

    /**
     * Rolls back a transaction
     *
     */
    public void rollback(Transaction t) {
    }

    protected Object comm(MessageType t, String data, ByteCountedString... payload) {
        int length;
                
        // Calculate lengths
        length = data.length();
        for (ByteCountedString bcs : payload) {
            length += bcs.length();
        }
        
        Header request = new Header(Header.DEFAULT_MAGIC_NUMBER, (byte)1, (byte)0, t, false, length);
        Header response;
        
        // Send header, data and payload and read response header
        try {
            request.writeTo(this.out);
            this.out.writeBytes(data);
            for (ByteCountedString bcs : payload) {
                bcs.writeTo(this.out);
            }
            
            response = Header.readFrom(this.in);
        } catch (IOException e) {
            throw new RemoteException(e);
        }
        
        // Verify magic number matches
        if (Header.DEFAULT_MAGIC_NUMBER != response.getMagicNumber()) {
            String extra = "successfully";
            try {
                this.sock.close();
            } catch (IOException e) {
                extra = "with " + e.getMessage();
            }
            throw new RemoteException(String.format(
                "Magic number mismatch, have %x, expect %x (closed communications %s)",
                response.getMagicNumber(),
                Header.DEFAULT_MAGIC_NUMBER,
                extra
            ));
        }
        
        // Perform actions based on message type
        try {
            switch (response.getMessageType()) {
                case Value: {
                    String serialized = ByteCountedString.readFrom(this.in);
                    return Serializer.valueOf(serialized, new SerializerContext(this));
                }
                
                case Exception: {
                    String serialized = ByteCountedString.readFrom(this.in);
                    throw (Exception)Serializer.valueOf(serialized, new SerializerContext(this));
                }
                
                case Error: {
                    String message = ByteCountedString.readFrom(this.in);
                    this.sock.close();
                    throw new RemoteException(message);
                }
                
                default: {
                    this.sock.close();
                    throw new RemoteException("Unknown message type");
                }
            }
        } catch (Exception e) {
            throw new RemoteException(e);
        }
    }
}
