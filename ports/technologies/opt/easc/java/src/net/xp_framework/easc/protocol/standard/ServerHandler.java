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
import net.xp_framework.easc.util.ByteCountedString;
import net.xp_framework.easc.server.Delegate;
import net.xp_framework.easc.protocol.standard.Header;
import net.xp_framework.easc.server.ServerContext;

import static net.xp_framework.easc.protocol.standard.Header.DEFAULT_MAGIC_NUMBER;

abstract public class ServerHandler implements Handler {

    /**
     * Write response
     *
     * @access  protected
     * @param   java.io.DataOutputStream out
     * @param   net.xp_framework.easc.protocol.standard.MessageType type
     * @param   java.lang.String buffer the encoded data
     */
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

        // Write data and flush
        bytes.writeTo(out);
        out.flush();
    }

    /**
     * Handle client connection
     *
     * @access  public
     * @param   java.io.DataInputStream in
     * @param   java.io.DataOutputStream out
     * @param   net.xp_framework.easc.server.ServerContext ctx
     */
    public void handle(DataInputStream in, DataOutputStream out, final ServerContext ctx) {        
        boolean done= false;
        while (!done) {
            try {
                Header requestHeader= Header.readFrom(in);

                // Verify magic number
                if (DEFAULT_MAGIC_NUMBER != requestHeader.getMagicNumber()) {
                    this.writeResponse(out, MessageType.Error, "Magic number mismatch");
                    break;
                }

                // Execute using delegate
                Object result= null;
                MessageType response= MessageType.Value;;
                String buffer= null;
                try {
                    Delegate delegate= requestHeader.getMessageType().delegateFrom(in, ctx);
                    ClassLoader delegateCl = delegate.getClassLoader();
                    
                    if (null != delegateCl) {
                        Thread currentThread= Thread.currentThread();
                        ClassLoader currentCl= currentThread.getContextClassLoader();
                        currentThread.setContextClassLoader(delegateCl);
                        buffer= Serializer.representationOf(delegate.invoke(ctx), new SerializerContext(delegateCl));
                        currentThread.setContextClassLoader(currentCl);
                    } else {
                        buffer= Serializer.representationOf(delegate.invoke(ctx));
                    }
                } catch (Throwable t) {
                    // DEBUG t.printStackTrace();
                    try {
                        buffer= Serializer.representationOf(t);
                        response= MessageType.Exception;
                    } catch (Exception e) {
                        buffer= e.getMessage();
                        response= MessageType.Error;
                    }
                }

                // Write result
                this.writeResponse(out, response, buffer);
            } catch (IOException e) {
                // Presumably, the client has closed the connection
                done= true;
            }
        }
    }
}
