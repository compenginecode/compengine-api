#include <math.h>
#include <stdlib.h>
#include <string.h>
#include <stdio.h>
#include "helper_functions.h"

double min(double a[], int size)
{
    double m = a[0];
    for (int i = 1; i < size; i++) {
        if (a[i] < m) {
            m = a[i];
        }
    }
    return m;
}

double max(double a[], int size)
{
    double m = a[0];
    for (int i = 1; i < size; i++) {
        if (a[i] > m) {
            m = a[i];
        }
    }
    return m;
}

double mean(double a[], int size)
{
    double m = 0.0;
    for (int i = 0; i < size; i++) {
        m += a[i];
    }
    m /= size;
    return m;
}

double median(double a[], int size)
{
    double m;
    double * b = malloc(size * sizeof *b);
    memcpy(b, a, size * sizeof *b);
    sort(b, size);
    if (size % 2 == 1) {
        m = b[size / 2];
    } else {
        int m1 = size / 2;
        int m2 = m1 - 1;
        m = (b[m1] + b[m2]) / (double)2.0;
    }
    free(b);
    return m;
}

double stddev(double a[], int size)
{
    double m = mean(a, size);
    double sd = 0.0;
    for (int i = 0; i < size; i++) {
        sd += pow(a[i] - m, 2);
    }
    sd = sqrt(sd / (size - 1));
    return sd;
}

void zscore_norm(double a[], int size)
{
    double m = mean(a, size);
    double sd = stddev(a, size);
    for (int i = 0; i < size; i++) {
        a[i] = (a[i] - m) / sd;
    }
    return;
}

void zscore_norm2(double a[], int size, double b[])
{
    double m = mean(a, size);
    double sd = stddev(a, size);
    for (int i = 0; i < size; i++) {
        b[i] = (a[i] - m) / sd;
    }
    return;
}

double moment(double a[], int size, int start, int end, int r)
{
    int win_size = end - start + 1;
    a += start;
    double m = mean(a, win_size);
    double mr = 0.0;
    for (int i = 0; i < win_size; i++) {
        mr += pow(a[i] - m, r);
    }
    mr /= win_size;
    mr /= stddev(a, win_size); //normalize
    return mr;
}

void diff(double a[], int size, double b[])
{
    for (int i = 1; i < size; i++) {
        b[i - 1] = a[i] - a[i - 1];
    }
}

int linreg(int n, const double x[], const double y[], double* m, double* b) //, double* r)
{
    double   sumx = 0.0;                      /* sum of x     */
    double   sumx2 = 0.0;                     /* sum of x**2  */
    double   sumxy = 0.0;                     /* sum of x * y */
    double   sumy = 0.0;                      /* sum of y     */
    double   sumy2 = 0.0;                     /* sum of y**2  */
    
    /*
    for (int i = 0; i < n; i++)
    {
        fprintf(stdout, "x[%i] = %f, y[%i] = %f\n", i, x[i], i, y[i]);
    }
    */
    
    for (int i=0;i<n;i++){
        sumx  += x[i];
        sumx2 += x[i] * x[i];
        sumxy += x[i] * y[i];
        sumy  += y[i];
        sumy2 += y[i] * y[i];
    }
    
    double denom = (n * sumx2 - sumx * sumx);
    if (denom == 0) {
        // singular matrix. can't solve the problem.
        *m = 0;
        *b = 0;
        //if (r) *r = 0;
        return 1;
    }
    
    *m = (n * sumxy  -  sumx * sumy) / denom;
    *b = (sumy * sumx2  -  sumx * sumxy) / denom;
    
    /*if (r!=NULL) {
        *r = (sumxy - sumx * sumy / n) /    // compute correlation coeff
        sqrt((sumx2 - sumx * sumx/n) *
             (sumy2 - sumy * sumy/n));
    }
    */
    
    return 0;
}

double norm(double a[], int size)
{
    
    double out = 0.0;
    
    for (int i = 0; i < size; i++)
    {
        out += a[i]*a[i];
    }
    
    out = sqrt(out);
    
    return out;
}
