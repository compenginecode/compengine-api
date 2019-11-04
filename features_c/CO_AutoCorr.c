#include <complex.h>
#include <math.h>
#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include "stats.h"
#include "fft.h"
#ifndef CMPLX
#define CMPLX(x, y) ((cplx)((double)(x) + _Imaginary_I * (double)(y)))
#endif
#define pow2(x) (1 << x)

typedef double complex cplx;

static int nextpow2(int n)
{
    n--;
    n |= n >> 1;
    n |= n >> 2;
    n |= n >> 4;
    n |= n >> 8;
    n |= n >> 16;
    n++;
    return n;
}

static void apply_conj(cplx a[], int size, int normalize)
{   
    switch(normalize) {
        case(1):
            for (int i = 0; i < size; i++) {
                a[i] = conj(a[i]) / size;
            }
            break;
        default:
            for (int i = 0; i < size; i++) {
                a[i] = conj(a[i]);
            }
            break;
    }
}

static void dot_multiply(cplx a[], cplx b[], int size)
{
    for (int i = 0; i < size; i++) {
        a[i] = a[i] * conj(b[i]);
    }
}

double * CO_AutoCorr(double y[], int size, int tau[], int tau_size)
{
    double m, nFFT;
    m = mean(y, size);
    nFFT = nextpow2(size) << 1;

    cplx * F = malloc(nFFT * sizeof *F);
    cplx * tw = malloc(nFFT * sizeof *tw);
    for (int i = 0; i < size; i++) {
        F[i] = CMPLX(y[i] - m, 0.0);
    }
    for (int i = size; i < nFFT; i++) {
        F[i] = CMPLX(0.0, 0.0);
    }
    size = nFFT;

    twiddles(tw, size);
    fft(F, size, tw);
    dot_multiply(F, F, size);
    fft(F, size, tw);
    cplx divisor = F[0];
    for (int i = 0; i < size; i++) {
        F[i] = F[i] / divisor;
    }
    
    double * out = malloc(tau_size * sizeof(out));
    for (int i = 0; i < tau_size; i++) {
        out[i] = creal(F[tau[i]]);
    }
    free(F);
    free(tw);
    return out;
}

double * co_autocorrs(double y[], int size)
{
    double m, nFFT;
    m = mean(y, size);
    nFFT = nextpow2(size) << 1;
    
    cplx * F = malloc(nFFT * sizeof *F);
    cplx * tw = malloc(nFFT * sizeof *tw);
    for (int i = 0; i < size; i++) {
        F[i] = CMPLX(y[i] - m, 0.0);
    }
    for (int i = size; i < nFFT; i++) {
        F[i] = CMPLX(0.0, 0.0);
    }
    size = nFFT;
    
    twiddles(tw, size);
    fft(F, size, tw);
    dot_multiply(F, F, size);
    fft(F, size, tw);
    cplx divisor = F[0];
    for (int i = 0; i < size; i++) {
        F[i] = F[i] / divisor;
    }
    
    double * out = malloc(size * sizeof(out));
    for (int i = 0; i < size; i++) {
        out[i] = creal(F[i]);
    }
    free(F);
    free(tw);
    return out;
}

int co_firstzero(double y[], int size, int maxtau)
{
    
    double * autocorrs = malloc(size * sizeof * autocorrs);
    
    autocorrs = co_autocorrs(y, size);
    
    int zerocrossind = 0;
    while(autocorrs[zerocrossind] > 0 && zerocrossind < maxtau)
    {
        zerocrossind += 1;
    }
    
    free(autocorrs);
    return zerocrossind;
    
}

double CO_Embed2_Basic_tau_incircle(double y[], int size, double radius, int tau)
{
    
    if( tau < 0)
    {
        tau = co_firstzero(y, size, size);
    }
    
    double insidecount = 0;
    for(int i = 0; i < size-tau; i++)
    {
        if(y[i]*y[i] + y[i+tau]*y[i+tau] < radius)
        {
            insidecount += 1;
        }
    }
    
    return insidecount/(size-tau);
}

int CO_FirstMin_ac(double y[], int size)
{
    
    double * autocorrs = NULL;
    
    autocorrs = co_autocorrs(y, size);
    
    int minInd = size;
    for(int i = 1; i < size-1; i++)
    {
        if(autocorrs[i] < autocorrs[i-1] && autocorrs[i] < autocorrs[i+1])
        {
            minInd = i;
            break;
        }
    }
    
    free(autocorrs);
    
    return minInd;
    
}

double CO_trev_1_num(double y[], int size)
{
    
    int tau = 1;
    
    double * diffTemp = malloc((size-1) * sizeof * diffTemp);
    
    for(int i = 0; i < size-tau; i++)
    {
        diffTemp[i] = pow(y[i+1] - y[i],3);
    }
    
    double out;
    
    out = mean(diffTemp, size-tau);
    
    free(diffTemp);
    
    return out;
}














