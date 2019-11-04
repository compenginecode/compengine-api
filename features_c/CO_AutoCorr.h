#ifndef CO_AUTOCORR_H
#define CO_AUTOCORR_H
#include <complex.h>
#include <math.h>
#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include "stats.h"
#include "fft.h"

extern double * CO_AutoCorr(double y[], int size, int tau[], int tau_size);
extern double * co_autocorrs(double y[], int size);
extern int co_firstzero(double y[], int size, int maxtau);
extern double CO_Embed2_Basic_tau_incircle(double y[], int size, double radius, int tau);
extern int CO_FirstMin_ac(double y[], int size);
extern double CO_trev_1_num(double y[], int size);

#endif
