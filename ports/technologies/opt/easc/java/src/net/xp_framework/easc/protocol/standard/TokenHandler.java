package net.xp_framework.easc.protocol.standard;

public interface TokenHandler {

    Object handle(String serialized, Length length, SerializerContext context, Class clazz) throws Exception;
}
