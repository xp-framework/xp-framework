/* This file is part of the XP extension "panter"
 *
 * $Id$ 
 */

#define RMI_GET          0
#define RMI_SET          1
#define RMI_INVOKE       2
#define RMI_EXCEPTION    3

#define RMI_SUCCESS      0
#define RMI_EMETHOD      1
#define RMI_ECHECKSUM    2

typedef struct _rmirequest {
    int method;
    int length;
    char* data;
    int data_len;
    char* class;
    int class_len;
    char* member;
    int member_len;
} rmirequest;

typedef struct _rmiresponse {
    int method;
    int length;
    char* data;
} rmiresponse;

int parserequest(const char* str, rmirequest** request);
int responsestring(rmiresponse* rmiresponse, char** str, int* len);
