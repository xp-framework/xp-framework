#include <stdio.h>

#define T_STRING 1
#define T_INT 2

#define STRVAL(x) T_STRING, x, 0
#define STRVAL_D(x) { STRVAL(x) }
#define INTVAL(x) T_INT, NULL, x
#define INTVAL_D(x) { INTVAL(x) }

typedef struct {
    int key;
    struct {
        int type;
        char *strval;
        int intval;
    } value;
} hash;

static void hashadd(hash *h, int key, int type, char *strval, int intval)
{
    h->key= key;
    h->value.type= type;
    h->value.strval= strval;
    h->value.intval= intval;
}

static void printhash(char *name, hash *h)
{
    printf("%s = {\n", name);
    while (h->key != NULL) {
        if (h->value.type == T_STRING) {
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
        { 1, STRVAL_D("Hello") },
        { 2, STRVAL_D("World") },
        { 3, INTVAL_D(6100) },
        { NULL }
    };
    hash b[3];
    hash *c;
    
    printhash("a", a);
    
    b[0].key= 1;
    b[0].value.strval= "Dynamic";
    b[0].value.type= T_STRING;
    hashadd(&b[1], 32, INTVAL(1));
    
    b[2].key= NULL;
    printhash("b", b);
    
    c= (hash *) malloc(sizeof(hash) * 2);
    c[0].key= 1;
    c[0].value.strval= "Pointer";
    c[0].value.type= T_STRING;
    c[1].key= NULL;
    printhash("c", c);
    free(c);
    
    return 0;
}
