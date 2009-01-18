package net.xp_framework.easc.util;

public class ExceptionReference extends RuntimeException {
    protected String message;

    public ExceptionReference(String className) {
        super(className);
    }
    
    public void setMessage(String message) {
        this.message = message;
    }

    @Override public String getLocalizedMessage() {
        return this.message;
    }
}
