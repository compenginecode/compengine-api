#ifndef FC_LOCALSIMPLE_H
#define FC_LOCALSIMPLE_H
#include <math.h>
#include <string.h>
#include "stats.h"
#include "CO_AutoCorr.h"

extern double fc_local_simple(double y[], int size, int train_length);
extern double FC_LocalSimple_mean_taures(double y[], int size, int train_length);
extern double FC_LocalSimple_lfit_taures(double y[], int size);

#endif
