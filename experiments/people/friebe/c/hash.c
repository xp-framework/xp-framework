#include <stdio.h>

#define STRING 1
#define INT 2

typedef struct {
    int key;
    int type;
    struct {
        char *strval;
        int intval;
    } value;
} hash;

static void printhash(char *name, hash *h)
{
    printf("%s = {\n", name);
    while (h->key != NULL) {
        if (h->type == STRING) {
            printf("  %d: (string)%s\n", h->key, h->value.strval);
        } else {
            printf("  %d: (int)%d\n", h->key, h->value.intval);
        }
        h++;
    }
    printf("}\n");
}

int main(int argc, char **argv)
{
    hash a[] = {
        { 1, STRING, { "Hello", 0 } },
        { 2, STRING, { "World", 0 } },
        { 3, INT, { NULL, 1 } },
        { NULL }
    };
    hash b[2];
    hash *c;
    
    printhash("a", a);
    
    b[0].key= 1;
    b[0].value.strval= "Dynamic";
    b[0].type= STRING;
    b[1].key= NULL;
    printhash("b", b);
    
    c= (hash *) malloc(sizeof(hash) * 2);
    c[0].key= 1;
    c[0].value.strval= "Pointer";
    c[0].type= STRING;
    c[1].key= NULL;
    printhash("c", c);
    free(c);
    
    return 0;
}
