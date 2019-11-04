#include <math.h>
#include <string.h>
#include <stdio.h>
#include "stats.h"

static void momentum(double a[], int size, double w[], double params[])
{
    double mass = params[0];
    double w_inert;
    w[0] = a[0];
    w[1] = a[1];
    for (int i = 2; i < size; i++) {
        w_inert = w[i - 1] + (w[i - 1] - w[i - 2]);
        w[i] = w_inert + (a[i] - w_inert) / mass;
    }
    return;
}

static void biasprop(double a[], int size, double w[], double params[])
{
    double up = params[0];
    double down = params[1];
    w[0] = 0;
    for (int i = 1; i < size; i++) {
        if (a[i] > a[i - 1]) {
            w[i] = w[i - 1] + up * (a[i - 1] - w[i - 1]);
        } else {
            w[i] = w[i - 1] + down * (a[i - 1] - w[i - 1]);
        }
    }
}

double ph_walker(double y[], int size, char rule[], double params[])
{
    double * w = malloc(size * sizeof *w);
    double stat = 0;
    if (strcmp(rule, "momentum") == 0) {
        momentum(y, size, w, params);
        stat = max(w, size);
    } else if (strcmp(rule, "biasprop") == 0) {
        biasprop(y, size, w, params);
        stat = mean(w, size);
    }
    free(w);
    return stat;
}

double PH_Walker_prop_01_sw_propcross(double y[], int size)
{
    
    double p = 0.1;
    double * w = malloc(size * sizeof * w);
    
    w[0] = 0;
    
    for(int i = 1; i < size; i++)
    {
        w[i] = w[i-1] + p * (y[i-1] - w[i-1]);
    }
    
    double out = 0.0;
    
    for(int i = 0; i < size-1; i++)
    {
        double temp = (w[i] - y[i])*(w[i+1] - y[i+1]);
        out += (temp < 0) ? 1 : 0;
    }
    
    out /= size - 1;
    
    free(w);
    
    return out;
}
