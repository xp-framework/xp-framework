/* This file is part of the XP framework
 *
 * $Id$ 
 */

#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <glib.h>
#include <gdk/gdk.h>
#include <gtk/gtk.h>
#include <gdk/gdkx.h>
#include <gdk-pixbuf/gdk-pixbuf.h>

GdkPixbuf* makeshot() {
  GdkWindow *window;
  GdkPixbuf *screenshot;
  GdkRectangle clip;
  GdkScreen *cur_screen;
  GdkPoint origin;
  gint screen_w, screen_h;

  /* use default screen if we are running non-interactively */
  cur_screen = gdk_screen_get_default ();
  if (!cur_screen) {
    printf ("No screen available.\n");
    return NULL;
  }

  screen_w= gdk_screen_get_width (cur_screen);
  screen_h= gdk_screen_get_height (cur_screen);

  window= gdk_screen_get_root_window (cur_screen);

  if (!window) {
    printf ("Could not find window!\n");
    return NULL;
  }

  gdk_drawable_get_size (GDK_DRAWABLE (window), &clip.width, &clip.height);
  printf ("Window size is %d x %d\n", clip.width, clip.height);
  gdk_window_get_origin (window, &origin.x, &origin.y);
  printf ("Origin is %d, %d\n", origin.x, origin.y);

  clip.x= 0; clip.y= 0;
  /* do clipping */
  if (origin.x < 0) {
    clip.x = -origin.x;
    clip.width += origin.x;
  }

  if (origin.y < 0) {
    clip.y = -origin.y;
    clip.height += origin.y;
  }

  if (origin.x + clip.width > screen_w)
    clip.width -= origin.x + clip.width - screen_w;

  if (origin.y + clip.height > screen_h)
    clip.height -= origin.y + clip.height - screen_h;

  printf ("clip.x: %d\nclip.y: %d\nwidth: %d\nheight: %d\n",
    clip.x, clip.y, clip.width, clip.height);

  if (NULL == (screenshot = gdk_pixbuf_get_from_drawable (
    NULL, 
    window,
    gdk_colormap_get_system(), 
    clip.x, 
    clip.y, 
    0, 
    0,
    clip.width, 
    clip.height
  ))) {
    printf ("Could not retrieve screen data.\n");
    return NULL;
  }
  
  gdk_display_beep (gdk_screen_get_display (cur_screen));
  gdk_flush ();
  return screenshot;
}

gboolean saveGdkPixbuf(GdkPixbuf *pix, char *filename) {
  GError *err;
  gboolean res;

  err= NULL;
  if (FALSE == (res= gdk_pixbuf_save (
    pix,
    filename,
    "png",
    &err,
    NULL
  ))) {
    printf ("gdk_pixbuf_save(): Error %d: %s",
      err->code,
      err->message
    );
  }
  return res;
}

int main(int argc, char *argv[]) {
  GdkPixbuf *screenshot;
  
  if (FALSE == gdk_init_check(&argc, &argv)) {
    printf ("gdk_init_check() failed.\n");
    exit (1);
  }

  g_type_init();


  if (!(screenshot= makeshot())) {
    printf ("makeshot(): failed to aquire screenshot!\n");
    return 1;
  }

  saveGdkPixbuf(screenshot, "screen.png");

  return 0;
}
