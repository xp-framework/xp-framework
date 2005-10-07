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
        try {
            (new LoginContext("easc", (CallbackHandler) new CallbackHandler() {
                public void handle(Callback[] callbacks) throws UnsupportedCallbackException {
                    for (int i= 0; i < callbacks.length; i++) {
                        if (callbacks[i] instanceof NameCallback) {
                            ((NameCallback)callbacks[i]).setName(username);
                        } else if (callbacks[i] instanceof PasswordCallback) {
                            ((PasswordCallback)callbacks[i]).setPassword(password.toCharArray());
                        }
                    }
                }
            })).login();
        } catch (LoginException e) {
            System.out.println(e);
        }
    }

    public Object invoke(ProxyMap map) throws Exception {
        return true;
    }
}
