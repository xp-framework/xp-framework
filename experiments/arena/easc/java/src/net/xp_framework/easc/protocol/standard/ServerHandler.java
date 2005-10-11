/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.protocol.standard;

import java.io.DataInputStream;
import java.io.DataOutputStream;
import java.io.IOException;
import java.lang.reflect.Proxy;
import net.xp_framework.easc.server.Handler;
import net.xp_framework.easc.protocol.standard.MessageType;
import net.xp_framework.easc.util.ByteCountedString;
import net.xp_framework.easc.server.Delegate;
import net.xp_framework.easc.protocol.standard.Header;
import net.xp_framework.easc.server.ServerContext;

import static net.xp_framework.easc.protocol.standard.Header.DEFAULT_MAGIC_NUMBER;

public class ServerHandler implements Handler {

    static {
        Serializer.registerExceptionName(javax.naming.NameNotFoundException.class, "naming/NameNotFound");
        Serializer.registerExceptionName(java.lang.reflect.InvocationTargetException.class, "invoke/Exception");
    }
    
    protected void writeResponse(DataOutputStream out, MessageType type, String buffer) throws IOException {
        ByteCountedString bytes= new ByteCountedString(buffer);

        // Write header
        new Header(
            DEFAULT_MAGIC_NUMBER,
            (byte)1,
            (byte)0,
            type,
            false,
            bytes.length()
        ).writeTo(out);

        // Write length bytes
        bytes.writeTo(out);
        
        // DEBUG System.out.println("[EASC] SEND " + type + " ('" + buffer + "')");
        
        out.flush();
    }

    public void handle(DataInputStream in, DataOutputStream out, final ServerContext ctx) throws IOException {        
        Serializer.registerMapping(Proxy.class, new Invokeable<String, Proxy>() {
            public String invoke(Proxy p) throws Exception {
                ctx.put(p.hashCode(), p);
                return "I:" + p.hashCode() + ":{" + Serializer.representationOf(
                    p.getClass().getInterfaces()[0].getName()
                ) + "}";
            }
        });

        while (true) {
            Header requestHeader= Header.readFrom(in);

            // Verify magic number
            if (DEFAULT_MAGIC_NUMBER != requestHeader.getMagicNumber()) {
                this.writeResponse(out, MessageType.Error, "Magic number mismatch");
                break;
            }

            // DEBUG System.out.println("[EASC] GOT " + requestHeader.getMessageType());

            Delegate delegate= null;
            Object result= null;
            MessageType response= null;
            String buffer= null;
            try {
                delegate= requestHeader.getMessageType().delegateFrom(in, ctx);

                // DEBUG System.out.println("[EASC] DELEGATE = " + delegate);

                result= delegate.invoke(ctx);
                response= MessageType.Value;

                buffer= Serializer.representationOf(result);
            } catch (Throwable t) {
                t.printStackTrace();
                try {
                    buffer= Serializer.representationOf(t);
                    response= MessageType.Exception;
                } catch (Exception e) {
                    buffer= e.getMessage();
                    response= MessageType.Error;
                }
            }
            
            this.writeResponse(out, response, buffer);
        }
        
        // Close streams
        in.close();
        out.close();
    }
}
