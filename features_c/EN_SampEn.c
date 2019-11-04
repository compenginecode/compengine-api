#include <math.h>
#include <string.h>
#include <time.h>
#include <float.h>
#include <stdio.h>
#include <stdlib.h>

/* sampen() calculates an estimate of sample entropy but does NOT calculate
 the variance of the estimate */
double * sampen(double *y, int M, double r, int n)
{
    double *p = NULL;
    double *e = NULL;
    long *run = NULL, *lastrun = NULL, N;
    double *A = NULL, *B = NULL;
    int M1, j, nj, jj, m;
    int i;
    double y1;
    
    M++;
    if ((run = (long *) calloc(n, sizeof(long))) == NULL)
        exit(1);
    if ((lastrun = (long *) calloc(n, sizeof(long))) == NULL)
        exit(1);
    if ((A = (double *) calloc(M, sizeof(double))) == NULL)
        exit(1);
    if ((B = (double *) calloc(M, sizeof(double))) == NULL)
        exit(1);
    if ((p = (double *) calloc(M, sizeof(double))) == NULL)
        exit(1);
    
    /* start running */
    for (i = 0; i < n - 1; i++) // each point of the time series
    {
        nj = n - i - 1;
        y1 = y[i];
        for (jj = 0; jj < nj; jj++) // future TS points
        {
            j = jj + i + 1;
            if (((y[j] - y1) < r) && ((y1 - y[j]) < r)) {
                run[jj] = lastrun[jj] + 1;
                M1 = M < run[jj] ? M : run[jj];
                for (m = 0; m < M1; m++) {
                    A[m]++;
                    if (j < n - 1)
                        B[m]++;
                }
            }
            else
                run[jj] = 0;
        }            /* for jj */
        for (j = 0; j < nj; j++)
            lastrun[j] = run[j];
    }                /* for i */
    
    N = (long) (n * (n - 1) / 2);
    p[0] = A[0] / N;
    
    double * output = malloc((M+1) * sizeof * output);
    output[0] = -log(p[0]);
    
    for (m = 1; m < M; m++) {
        p[m] = A[m] / B[m - 1];
        if (p[m] == 0)
            output[m] = NAN;
        else
            output[m] = -log(p[m]);
    }
    
    free(A);
    free(B);
    free(p);
    free(run);
    free(lastrun);
    
    return output;
}

double EN_SampEn_5_03_sampen1(double *y, int n)
{
    double * result = NULL;
    
    result = sampen(y, 1, 0.3, n);
    
    double out = result[1];
    
    free(result);
    
    return out;
}
