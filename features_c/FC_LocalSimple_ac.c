#include <math.h>
#include <string.h>
#include "stats.h"
#include "CO_AutoCorr.h"

double * fc_local_simple_ac(double y[], int size, int train_length)
{
    int tau[] = {1};
    double * out;
    double * y1 = malloc((size - 1) * sizeof *y1);
    diff(y, size, y1);
    out = CO_AutoCorr(y1, size - 1, tau, 1);
    free(y1);
    return out;
}
