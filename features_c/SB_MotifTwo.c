#include <math.h>
#include <string.h>
#include <stdlib.h>
#include <stdio.h>
#include "stats.h"
#include "helper_functions.h"

// check for size of TS < 5 had to be moved out of function.
// It should be done in run_features before running sb_motiftwo
double * sb_motiftwo(double a[], int size, char how[])
{   
    double u, d, dd, ud, du, uu, ddd, ddu, dud, duu, udd, udu, uud, uuu;
    double dddd, dddu, ddud, dduu, dudd, dudu, duud, duuu, uddd, uddu, udud, uduu, uudd, uudu, uuud, uuuu;
    double h, hh, hhh, hhhh;
    // double NAN = 0.0/0.0; // need to move it to separate file for reusability
    int results_idx = 0;
    int subset_size = size;
    int tmp;
    double * results = malloc(34 * sizeof results);
    int * b = malloc(size * sizeof(int)); // binarized a
    binarize(a, size, b, how);
    double * r1 = malloc(size * sizeof(double));
    double * r0 = malloc(size * sizeof(double));
    for (int i = 0; i < size; i++) {
        r1[i] = (b[i] == 1) ? 1 : 0;
        r0[i] = (b[i] == 0) ? 1 : 0;
    }
    
    /*
    for(int i = 0; i < 100; i++)
    {
        fprintf(stdout, "%i: a=%f, b=%i, r1=%f, r0=%f\n", i, a[i], b[i], r1[i], r0[i]);
    }
     */

    // length 1
    u = mean(r1, subset_size);
    results[results_idx++] = u;
    d = mean(r0, subset_size);
    results[results_idx++] = d;
    double p[] = {u, d};
    h = f_entropy(p, 2);
    results[results_idx++] = h;
    subset_size--;

    // length 2
    r1 = realloc(r1, subset_size * sizeof(double));
    r0 = realloc(r0, subset_size * sizeof(double));
    int * b2 = malloc(subset_size * sizeof(int));
    subset(b, b2, 1, size);
    double * r00 = malloc(subset_size * sizeof(double));
    double * r01 = malloc(subset_size * sizeof(double));
    double * r10 = malloc(subset_size * sizeof(double));
    double * r11 = malloc(subset_size * sizeof(double));
    for (int i = 0; i < subset_size; i++) {
        tmp = (b2[i] == 0) ? 1 : 0;
        r00[i] = r0[i] * tmp;
        r10[i] = r1[i] * tmp;

        tmp = (b2[i] == 1) ? 1 : 0;
        r01[i] = r0[i] * tmp;
        r11[i] = r1[i] * tmp;
    }
    free(r1);
    free(r0);
    dd = mean(r00, subset_size);
    results[results_idx++] = dd;
    du = mean(r01, subset_size);
    results[results_idx++] = du;
    ud = mean(r10, subset_size);
    results[results_idx++] = ud;
    uu = mean(r11, subset_size);
    results[results_idx++] = uu;
    double pp[] = {dd, du, ud, uu};
    hh = f_entropy(pp, 4);
    results[results_idx++] = hh;
    subset_size--;

    // length 3
    r00 = realloc(r00, subset_size * sizeof(double));
    r01 = realloc(r01, subset_size * sizeof(double));
    r10 = realloc(r10, subset_size * sizeof(double));
    r11 = realloc(r11, subset_size * sizeof(double));
    b2 = realloc(b2, subset_size * sizeof(int));
    subset(b, b2, 2, size);
    double * r000 = malloc(subset_size * sizeof(double));
    double * r001 = malloc(subset_size * sizeof(double));
    double * r010 = malloc(subset_size * sizeof(double));
    double * r011 = malloc(subset_size * sizeof(double));
    double * r100 = malloc(subset_size * sizeof(double));
    double * r101 = malloc(subset_size * sizeof(double));
    double * r110 = malloc(subset_size * sizeof(double));
    double * r111 = malloc(subset_size * sizeof(double));
    for (int i = 0; i < subset_size; i++) {
        tmp = (b2[i] == 0) ? 1 : 0;
        r000[i] = r00[i] * tmp;
        r010[i] = r01[i] * tmp;
        r100[i] = r10[i] * tmp;
        r110[i] = r11[i] * tmp;

        tmp = (b2[i] == 1) ? 1 : 0;
        r001[i] = r00[i] * tmp;
        r011[i] = r01[i] * tmp;
        r101[i] = r10[i] * tmp;
        r111[i] = r11[i] * tmp;
    }
    free(r00);
    free(r01);
    free(r10);
    free(r11);
    ddd = mean(r000, subset_size);
    results[results_idx++] = ddd;
    ddu = mean(r001, subset_size);
    results[results_idx++] = ddu;
    dud = mean(r010, subset_size);
    results[results_idx++] = dud;
    duu = mean(r011, subset_size);
    results[results_idx++] = duu;
    udd = mean(r100, subset_size);
    results[results_idx++] = udd;
    udu = mean(r101, subset_size);
    results[results_idx++] = udu;
    uud = mean(r110, subset_size);
    results[results_idx++] = uud;
    uuu = mean(r111, subset_size);
    results[results_idx++] = uuu;
    // one udd should be removed if it is indeed a bug
    double ppp[] = {ddd, ddu, dud, duu, udd, udu, uud, uuu};
    hhh = f_entropy(ppp, 8);
    results[results_idx++] = hhh;
    subset_size--;

    // length 4
    r000 = realloc(r000, subset_size * sizeof(double));
    r001 = realloc(r001, subset_size * sizeof(double));
    r010 = realloc(r010, subset_size * sizeof(double));
    r011 = realloc(r011, subset_size * sizeof(double));
    r100 = realloc(r100, subset_size * sizeof(double));
    r101 = realloc(r101, subset_size * sizeof(double));
    r110 = realloc(r110, subset_size * sizeof(double));
    r111 = realloc(r111, subset_size * sizeof(double));
    b2 = realloc(b2, subset_size * sizeof(int));
    subset(b, b2, 3, size);
    double * r0000 = malloc(subset_size * sizeof(double));
    double * r0001 = malloc(subset_size * sizeof(double));
    double * r0010 = malloc(subset_size * sizeof(double));
    double * r0011 = malloc(subset_size * sizeof(double));
    double * r0100 = malloc(subset_size * sizeof(double));
    double * r0101 = malloc(subset_size * sizeof(double));
    double * r0110 = malloc(subset_size * sizeof(double));
    double * r0111 = malloc(subset_size * sizeof(double));
    double * r1000 = malloc(subset_size * sizeof(double));
    double * r1001 = malloc(subset_size * sizeof(double));
    double * r1010 = malloc(subset_size * sizeof(double));
    double * r1011 = malloc(subset_size * sizeof(double));
    double * r1100 = malloc(subset_size * sizeof(double));
    double * r1101 = malloc(subset_size * sizeof(double));
    double * r1110 = malloc(subset_size * sizeof(double));
    double * r1111 = malloc(subset_size * sizeof(double));
    for (int i = 0; i < subset_size; i++) {
        tmp = (b2[i] == 0) ? 1 : 0;
        r0000[i] = r000[i] * tmp;
        r0010[i] = r001[i] * tmp;
        r0100[i] = r010[i] * tmp;
        r0110[i] = r011[i] * tmp;
        r1000[i] = r100[i] * tmp;
        r1010[i] = r101[i] * tmp;
        r1100[i] = r110[i] * tmp;
        r1110[i] = r111[i] * tmp;
        
        tmp = (b2[i] == 1) ? 1 : 0;        
        r0001[i] = r000[i] * tmp;
        r0011[i] = r001[i] * tmp;
        r0101[i] = r010[i] * tmp;
        r0111[i] = r011[i] * tmp;
        r1001[i] = r100[i] * tmp;
        r1011[i] = r101[i] * tmp;
        r1101[i] = r110[i] * tmp;
        r1111[i] = r111[i] * tmp;
    }
    free(r000);
    free(r001);
    free(r010);
    free(r011);
    free(r100);
    free(r101);
    free(r110);
    free(r111);
    dddd = mean(r0000, subset_size);
    results[results_idx++] = dddd;
    dddu = mean(r0001, subset_size);
    results[results_idx++] = dddu;
    ddud = mean(r0010, subset_size);
    results[results_idx++] = ddud;
    dduu = mean(r0011, subset_size);
    results[results_idx++] = dduu;
    dudd = mean(r0100, subset_size);
    results[results_idx++] = dudd;
    dudu = mean(r0101, subset_size);
    results[results_idx++] = dudu;
    duud = mean(r0110, subset_size);
    results[results_idx++] = duud;
    duuu = mean(r0111, subset_size);
    results[results_idx++] = duuu;
    uddd = mean(r1000, subset_size);
    results[results_idx++] = uddd;
    uddu = mean(r1001, subset_size);
    results[results_idx++] = uddu;
    udud = mean(r1010, subset_size);
    results[results_idx++] = udud;
    uduu = mean(r1011, subset_size);
    results[results_idx++] = uduu;
    uudd = mean(r1100, subset_size);
    results[results_idx++] = uudd;
    uudu = mean(r1101, subset_size);
    results[results_idx++] = uudu;
    uuud = mean(r1110, subset_size);
    results[results_idx++] = uuud;
    uuuu = mean(r1111, subset_size);
    results[results_idx++] = uuuu;
    double pppp[] = {dddd, dddu, ddud, dduu, dudd, dudu, duud, duuu, uddd, uddu, udud, uduu, uudd, uudu, uuud, uuuu};
    hhhh = f_entropy(pppp, 16);
    results[results_idx++] = hhhh;
    free(r0000);
    free(r0001);
    free(r0010);
    free(r0011);
    free(r0100);
    free(r0101);
    free(r0110);
    free(r0111);
    free(r1000);
    free(r1001);
    free(r1010);
    free(r1011);
    free(r1100);
    free(r1101);
    free(r1110);
    free(r1111);

    free(b);
    free(b2);

    return results;
}

double SB_MotifTwo_mean_hhh(double a[], int size)
{
    double * output = NULL;
    
    output = sb_motiftwo(a, size, "mean");
    
    double out = output[16];
    
    free(output);
    
    return out;
}










