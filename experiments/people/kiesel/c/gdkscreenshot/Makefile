GNOMEDEPS="gdk-2.0 glib-2.0 atk"

GDKLIBS!=pkg-config --libs $(GNOMEDEPS)
GDKINC!=pkg-config --cflags $(GNOMEDEPS)

CFLAGS+=-Wall -ggdb -O -I. $(GDKINC)
#LDFLAGS+=$(GDKINC)
LDLIBS+=$(GDKLIBS) -lgd

default: build

build: screenshot.o
	$(CC) $(CFLAGS) $(LDLIBS) screenshot.o -o screenshot

clean:
	rm -rvf *.o *.core screenshot
