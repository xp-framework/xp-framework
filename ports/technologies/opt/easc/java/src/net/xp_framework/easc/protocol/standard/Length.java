package net.xp_framework.easc.protocol.standard;

public class Length {
    public int value = 0;

    public Length(int initial) {
        this.value = initial;
    }

    @Override public String toString() {
        return "Length(" + this.value + ")";
    }
}
