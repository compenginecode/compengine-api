#ifndef HELPER_FUNCTIONS_H
#define HELPER_FUNCTIONS_H
#include <string.h>
#include <stdlib.h>
#include <stdio.h>
#include "stats.h"

extern void linspace(double start, double end, int num_groups, double out[]);
extern double quantile(double y[], int size, double quant);
extern void sort(double y[], int size);
extern void binarize(double a[], int size, int b[], char how[]);
extern double f_entropy(double a[], int size);
extern void subset(int a[], int b[], int start, int end);

#endif