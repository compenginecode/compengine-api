#ifndef PH_WALKER_H
#define PH_WALKER_H
#include <math.h>
#include <string.h>
#include "stats.h"

extern double ph_walker(double y[], int size, char rule[], double params[]);
extern double PH_Walker_prop_01_sw_propcross(double y[], int size);

#endif
