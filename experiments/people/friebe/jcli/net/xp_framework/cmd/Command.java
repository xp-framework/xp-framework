package net.xp_framework.cmd;

import java.io.PrintStream;

public abstract class Command implements Runnable {
    public PrintStream out;
    public PrintStream err;

}
