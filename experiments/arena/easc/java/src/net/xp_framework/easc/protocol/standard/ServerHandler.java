/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.protocol.standard;

import java.io.DataInputStream;
import java.io.DataOutputStream;
import java.io.IOException;
import net.xp_framework.easc.server.Handler;
import net.xp_framework.easc.server.Delegate;
import net.xp_framework.easc.protocol.standard.Header;

import static net.xp_framework.easc.protocol.standard.Header.DEFAULT_MAGIC_NUMBER;

public class ServerHandler implements Handler {

    public void handle(DataInputStream in, DataOutputStream out) throws IOException {
        while (true) {
            Header requestHeader= Header.readFrom(in);

            // Verify magic number
            if (DEFAULT_MAGIC_NUMBER != requestHeader.getMagicNumber()) {
                out.writeUTF("-ERR MAGIC");
                out.flush();
                break;
            }

            System.out.print(requestHeader.getMessageType() + " => ");
            Delegate delegate= requestHeader.getMessageType().delegateFrom(in);

            try {
                out.writeUTF("+OK " + delegate.getClass().getName() + ": " + Serializer.representationOf(delegate.invoke()));
            } catch (Exception e) {
                e.printStackTrace(System.err);
                out.writeUTF("-ERR " + e.getClass().getName());
            }

            out.flush();
        }
    }
}
