package examples;

import net.xp_framework.cmd.*;

public class Named extends Command {
    protected String hostName;

    /**
     * Set host name
     *
     */
    @Arg public void setHost(String hostName) {
        this.hostName= hostName;
    }

    public void run() {
        this.out.println("Connecting to " + this.hostName);
    }
}
