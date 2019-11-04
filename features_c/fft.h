#ifndef FFT_H
#define FFT_H
#include <complex.h>
#include <stdlib.h>
#include <string.h>
#ifndef CMPLX
#define CMPLX(x, y) ((cplx)((double)(x) + _Imaginary_I * (double)(y)))
#endif
typedef double complex cplx;
extern void twiddles(cplx a[], int size);
// extern void _fft(cplx a[], cplx out[], int size, int step, cplx tw[]);
extern void fft(cplx a[], int size, cplx tw[]);
extern void ifft(cplx a[], int size, cplx tw[]);
#endif