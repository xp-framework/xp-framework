/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.protocol.standard;

import java.io.DataInputStream;
import java.io.DataOutputStream;
import java.io.IOException;
import net.xp_framework.easc.server.Handler;
import net.xp_framework.easc.protocol.standard.MessageType;
import net.xp_framework.easc.server.Delegate;
import net.xp_framework.easc.protocol.standard.Header;
import net.xp_framework.easc.server.ProxyMap;
import net.xp_framework.easc.server.ProxyWrapper;

import static net.xp_framework.easc.protocol.standard.Header.DEFAULT_MAGIC_NUMBER;

public class ServerHandler implements Handler {

    static {
        Serializer.registerMapping(ProxyWrapper.class, new Invokeable<String, ProxyWrapper>() {
            public String invoke(ProxyWrapper wrapper) throws Exception {
                return "I:" + wrapper.identifier + ":{" + Serializer.representationOf(
                    wrapper.object.getClass().getInterfaces()[0].getName()
                ) + "}";
            }
        });
        Serializer.registerExceptionName(javax.naming.NameNotFoundException.class, "naming/NameNotFound");
        Serializer.registerExceptionName(java.lang.reflect.InvocationTargetException.class, "invoke/Exception");
    }

    protected void writeResponse(DataOutputStream out, MessageType type, String buffer) throws IOException {
        new Header(
            DEFAULT_MAGIC_NUMBER,
            (byte)1,
            (byte)0,
            type,
            false,
            buffer.length()
        ).writeTo(out);
        out.writeUTF(buffer);
        
        // DEBUG System.out.println("[EASC] SEND " + type + " ('" + buffer + "')");
        
        out.flush();
    }

    public void handle(DataInputStream in, DataOutputStream out) throws IOException {
        ProxyMap map= new ProxyMap();
        while (true) {
            Header requestHeader= Header.readFrom(in);

            // Verify magic number
            if (DEFAULT_MAGIC_NUMBER != requestHeader.getMagicNumber()) {
                this.writeResponse(out, MessageType.Error, "Magic number mismatch");
                break;
            }

            // DEBUG System.out.println("[EASC] GOT " + requestHeader.getMessageType());

            Delegate delegate= null;
            try {
                delegate= requestHeader.getMessageType().delegateFrom(in);
            } catch (Throwable t) {
                t.printStackTrace();
                this.writeResponse(out, MessageType.Error, "Delegation error: " + t.getMessage());
                continue;
            }
                
            Object result= null;
            MessageType response= null;
            
            // DEBUG System.out.println("[EASC] DELEGATE = " + delegate);

            // Invoke the message
            try {
                result= delegate.invoke(map);
                response= MessageType.Value;
            } catch (Throwable t) {
                t.printStackTrace();
                result= t;
                response= MessageType.Exception;
            }
            
            // Serialize
            String buffer= null;
            try {
                buffer= Serializer.representationOf(result);
            } catch (Exception e) {
                e.printStackTrace();
                buffer= e.getMessage();
                response= MessageType.Error;
            }
            
            this.writeResponse(out, response, buffer);
        }
        
        // Close streams
        in.close();
        out.close();
    }
}
