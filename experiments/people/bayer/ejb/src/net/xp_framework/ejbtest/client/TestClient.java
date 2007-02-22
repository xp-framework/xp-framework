package ejbtest;

import javax.naming.directory.InitialDirContext;
import javax.naming.InitialContext;
import javax.naming.NamingException;
import java.util.Hashtable;

public class TestClient {

    public void run() {
        System.out.println("starting TestClient...");
        try {
            Hashtable env = new Hashtable();
            env.put(InitialContext.PROVIDER_URL, "jnp://localhost:1099");
            env.put(InitialContext.INITIAL_CONTEXT_FACTORY, "org.jnp.interfaces.NamingContextFactory");
            InitialContext ctx = new InitialContext(env);
            EngineList list = (EngineList)ctx.lookup("/HelloWorldBean/remote");
	    System.out.println(hw.sayHello("wurst"));
        } catch (NamingException e) {
            e.printStackTrace();
            return;
        }
    }

    public static void main(String[] argv) {
        HelloWorldClient c = new HelloWorldClient();
        c.run();
    }

}

