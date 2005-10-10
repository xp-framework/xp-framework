/* This class is part of the XP framework's EAS connectivity
 *
 * $Id$
 */

package net.xp_framework.easc.server;

import net.xp_framework.easc.server.Delegate;
import net.xp_framework.easc.server.ProxyMap;
import javax.security.auth.callback.Callback;
import javax.security.auth.callback.CallbackHandler;
import javax.security.auth.callback.NameCallback;
import javax.security.auth.callback.PasswordCallback;
import javax.security.auth.callback.UnsupportedCallbackException;
import javax.security.auth.login.LoginContext;
import javax.security.auth.login.LoginException;

public class InitializationDelegate implements Delegate {

    public InitializationDelegate() {
    }

    public InitializationDelegate(final String username, final String password) {
        // FIXME: Not yet implemented
    }

    public Object invoke(ProxyMap map) throws Exception {
        return true;
    }
}
