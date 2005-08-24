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

import static net.xp_framework.easc.protocol.standard.Header.DEFAULT_MAGIC_NUMBER;

public class ServerHandler implements Handler {

    protected void writeResponse(DataOutputStream out, MessageType type, String buffer) throws IOException {
        new Header(
            DEFAULT_MAGIC_NUMBER,
            (byte)1,
            (byte)0,
            type,
            false,
            0
        ).writeTo(out);
        out.writeUTF(buffer);
        out.flush();
    }

    public void handle(DataInputStream in, DataOutputStream out) throws IOException {
        while (true) {
            Header requestHeader= Header.readFrom(in);

            // Verify magic number
            if (DEFAULT_MAGIC_NUMBER != requestHeader.getMagicNumber()) {
                this.writeResponse(out, MessageType.Error, "Magic number mismatch");
                break;
            }

            Delegate delegate= requestHeader.getMessageType().delegateFrom(in);
            Object result= null;
            MessageType response= null;

            // Invoke the message
            try {
                result= delegate.invoke();
                response= MessageType.Value;
            } catch (Exception e) {
                result= e;
                response= MessageType.Exception;
            }
            
            // Serialize
            String buffer= null;
            try {
                buffer= Serializer.representationOf(result);
            } catch (Exception e) {
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
