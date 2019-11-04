#ifndef STATS_H
#define STATS_H
#include <math.h>
#include <stdlib.h>
#include <string.h>

extern double max(double a[], int size);
extern double min(double a[], int size);
extern double mean(double a[], int size);
extern double median(double a[], int size);
extern double stddev(double a[], int size);
extern void zscore_norm(double a[], int size);
extern void zscore_norm2(double a[], int size, double b[]);
extern double moment(double a[], int size, int start, int end, int r);
extern void diff(double a[], int size, double b[]);
extern int linreg(int n, const double x[], const double y[], double* m, double* b); //, double* r);
extern double norm(double a[], int size);

#endif
