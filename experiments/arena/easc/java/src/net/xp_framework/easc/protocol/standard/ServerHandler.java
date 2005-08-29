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
        
        // System.out.println("SEND " + type + " ('" + buffer + "')");
        
        out.flush();
    }

    public void handle(DataInputStream in, DataOutputStream out) throws IOException {
        ProxyMap map= new ProxyMap();
        Serializer.registerMapping(ProxyWrapper.class, new Invokeable<String, ProxyWrapper>() {
            public String invoke(ProxyWrapper wrapper) throws Exception {
                return "I:" + wrapper.identifier + ":{" + Serializer.representationOf(
                    wrapper.object.getClass().getInterfaces()[0].getName()
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

            // System.out.println("GOT " + requestHeader.getMessageType());

            Delegate delegate= requestHeader.getMessageType().delegateFrom(in);
            Object result= null;
            MessageType response= null;

            // Invoke the message
            try {
                result= delegate.invoke(map);
                response= MessageType.Value;
            } catch (Exception e) {
                e.printStackTrace();
                result= e;
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
            
            writeResponse(out, response, buffer);
        }
        
        // Close streams
        in.close();
        out.close();
    }
}
