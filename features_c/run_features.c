#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <dirent.h>
#include <unistd.h>
#include <math.h>
#include "CO_AutoCorr.h"
#include "FC_LocalSimple.h"
#include "SY_SpeardRandomLocal.h"
#include "DN_HistogramMode_10.h"
#include "FC_LocalSimple_ac.h"
#include "SY_SlidingWindow.h"
#include "PH_Walker.h"
#include "SY_StdNthDer.h"
#include "SB_MotifTwo.h"
#include "SB_CoarseGrain.h"
#include "SB_MotifThree.h"
#include "EN_SampEn.h"
#include "DN_OutlierInclude.h"
#include "SC_FluctAnal.h"

int quality_check(double y[], int size)
{
    int minSize = 100;
    
    if(size < minSize)
    {
        return 1;
    }
    for(int i = 0; i < size; i++)
    {
        double val = y[i];
        if(val == INFINITY || -val == INFINITY)
        {
            return 2;
        }
        if(isnan(val))
        {
            return 3;
        }
    }
    return 0;
}

void run_features(double y[], int size, FILE * outfile)
{
    int quality = quality_check(y, size);
    if(quality != 0)
    {
        fprintf(stdout, "Time series quality test not passed (code %i).\n", quality);
        return;
    }
    
    double out;
    int outInt;
    double * out_ar;
    double * y_zscored = malloc(size * sizeof * y_zscored);
    
    clock_t begin;
    double timeTaken;
    
    int tau[] = {9};
    int tau_size = 1;
    int i;

    double * copy = malloc(size * sizeof *copy);
    
    zscore_norm2(y, size, y_zscored);
    
    memcpy(copy, y, size * sizeof *copy);
    begin = clock();
    out = CO_Embed2_Basic_tau_incircle(copy, size, 1, -1);
    timeTaken = (double)(clock()-begin)*1000/CLOCKS_PER_SEC;
    fprintf(outfile, "%.14f, %s, %f\n", out, "CO_Embed2_Basic_tau.incircle_1", timeTaken);
    
    memcpy(copy, y, size * sizeof *copy);
    begin = clock();
    out = CO_Embed2_Basic_tau_incircle(copy, size, 2, -1);
    timeTaken = (double)(clock()-begin)*1000/CLOCKS_PER_SEC;
    fprintf(outfile, "%.14f, %s, %f\n", out, "CO_Embed2_Basic_tau.incircle_2", timeTaken);
    
    memcpy(copy, y, size * sizeof *copy);
    begin = clock();
    out = FC_LocalSimple_mean_taures(copy, size, 1);
    timeTaken = (double)(clock()-begin)*1000/CLOCKS_PER_SEC;
    fprintf(outfile, "%.14f, %s, %f\n", out, "FC_LocalSimple_mean1.taures", timeTaken);
    
    memcpy(copy, y_zscored, size * sizeof *copy);
    begin = clock();
    out = DN_HistogramMode_10(copy, size);
    timeTaken = (double)(clock()-begin)*1000/CLOCKS_PER_SEC;
    fprintf(outfile, "%.14f, %s, %f\n", out, "DN_HistogramMode_10", timeTaken);
    
    memcpy(copy, y_zscored, size * sizeof *copy);
    begin = clock();
    out = SY_StdNthDer(copy, size, 1);
    timeTaken = (double)(clock()-begin)*1000/CLOCKS_PER_SEC;
    fprintf(outfile, "%.14f, %s%i, %f\n", out, "SY_StdNthDer_", 1, timeTaken);
    
    memcpy(copy, y, size * sizeof *copy);
    begin = clock();
    out_ar = CO_AutoCorr(copy, size, tau, tau_size);
    timeTaken = (double)(clock()-begin)*1000/CLOCKS_PER_SEC;
    for (i = 0; i < tau_size; i++) {
        fprintf(outfile, "%.14f, %s%i, %f\n", out_ar[i], "AC_",tau[i], timeTaken);
    }
    free(out_ar);
    
    memcpy(copy, y, size * sizeof *copy);
    begin = clock();
    out = SB_MotifTwo_mean_hhh(copy, size);
    timeTaken = (double)(clock()-begin)*1000/CLOCKS_PER_SEC;
    fprintf(outfile, "%.14f, %s, %f\n", out, "SB_MotifTwo_mean.hhh", timeTaken);
    
    memcpy(copy, y, size * sizeof *copy);
    begin = clock();
    outInt = CO_FirstMin_ac(copy, size);
    timeTaken = (double)(clock()-begin)*1000/CLOCKS_PER_SEC;
    fprintf(outfile, "%i, %s, %f\n", outInt, "CO_FirstMin_ac", timeTaken);
    
    memcpy(copy, y_zscored, size * sizeof *copy);
    begin = clock();
    out = DN_OutlierInclude_abs_001(copy, size);
    timeTaken = (double)(clock()-begin)*1000/CLOCKS_PER_SEC;
    fprintf(outfile, "%.14f, %s, %f\n", out, "DN_OutlierInclude_abs_001.mdrmd", timeTaken);
    
    // output of this feature scaled
    memcpy(copy, y_zscored, size * sizeof *copy);
    begin = clock();
    out = CO_trev_1_num(copy, size);
    timeTaken = (double)(clock()-begin)*1000/CLOCKS_PER_SEC;
    fprintf(outfile, "%.14f, %s, %f\n", out, "CO_trev_1.num", timeTaken);
    
    memcpy(copy, y_zscored, size * sizeof *copy);
    begin = clock();
    out = SY_SpreadRandomLocal_50_100_meantaul(copy, size, 'n');
    timeTaken = (double)(clock()-begin)*1000/CLOCKS_PER_SEC;
    fprintf(outfile, "%.14f, %s, %f\n", out, "SY_SpreadRandomLocal_50_100.meantaul", timeTaken);
    
    memcpy(copy, y, size * sizeof *copy);
    begin = clock();
    out = SC_FluctAnal_2_rsrangefit_50_1_logi_prop_r1(copy, size);
    timeTaken = (double)(clock()-begin)*1000/CLOCKS_PER_SEC;
    fprintf(outfile, "%.14f, %s, %f\n", out, "SC_FluctAnal_2_rsrangefit_50_1_logi.prop_r1", timeTaken);
    
    memcpy(copy, y, size * sizeof *copy);
    begin = clock();
    out = PH_Walker_prop_01_sw_propcross(copy, size);
    timeTaken = (double)(clock()-begin)*1000/CLOCKS_PER_SEC;
    fprintf(outfile, "%.14f, %s, %f\n", out, "PH_Walker_prop_01.sw_propcross", timeTaken);
    
    // the following are the three slowest features
    
    memcpy(copy, y, size * sizeof *copy);
    begin = clock();
    out = SY_SpreadRandomLocal_ac2_100_meantaul(copy, size, 'n');
    timeTaken = (double)(clock()-begin)*1000/CLOCKS_PER_SEC;
    fprintf(outfile, "%.14f, %s, %f\n", out, "SY_SpreadRandomLocal_ac2_100.meantaul", timeTaken);
    
    memcpy(copy, y, size * sizeof *copy);
    begin = clock();
    out = FC_LocalSimple_lfit_taures(copy, size);
    timeTaken = (double)(clock()-begin)*1000/CLOCKS_PER_SEC;
    fprintf(outfile, "%.14f, %s, %f\n", out, "FC_LocalSimple_lfit.taures", timeTaken);
    
    memcpy(copy, y_zscored, size * sizeof *copy);
    begin = clock();
    out = EN_SampEn_5_03_sampen1(copy, size);
    timeTaken = (double)(clock()-begin)*1000/CLOCKS_PER_SEC;
    fprintf(outfile, "%.14f, %s, %f\n", out, "EN_SampEn_5_03.sampen1", timeTaken);
    
    // below features are not used
    
    /*
     memcpy(copy, y, size * sizeof *copy);
     out = fc_local_simple(copy, size, 1);
     fprintf(outfile, "%f, %s \n", out, "fc_local_simple");
     */
    
    /*
     memcpy(copy, y, size * sizeof *copy);
     out = sy_sliding_window(copy, size, 5, 2);
     fprintf(outfile, "%f, %s \n", out, "sy_sliding_window");
     */
    
    /*
     memcpy(copy, y, size * sizeof *copy);
     out = ph_walker(copy, size, "momentum", mom);
     fprintf(outfile, "%f, %s \n", out, "ph_walker_momentum");
     
     memcpy(copy, y, size * sizeof *copy);
     out = ph_walker(copy, size, "biasprop", bias);
     fprintf(outfile, "%f, %s \n", out, "ph_walker_biasprop");
     */
    
    /*
     memcpy(copy, y, size * sizeof *copy);
     out_ar = fc_local_simple_ac(copy, size, 1);
     fprintf(outfile, "%f, %s \n", out_ar[0], "fc_local_simple_ac");
     free(out_ar);
     */
    
    /*
     memcpy(copy, y, size * sizeof *copy);
     out_ar = sb_motiftwo(copy, size, "mean"); // sb_motiftwo_mean_hhh/duud
     for (i = 0; i < sb_motiftwo_out_size; i++) {
     fprintf(outfile, "%f, %s%i \n", out_ar[i], "sb_motiftwo_mean_", i);
     }
     free(out_ar);
     */
    
    /*
     memcpy(copy, y, size * sizeof *copy);
     out_ar = sb_motifthree(y, size, "quantile");
     for (i = 0; i < sb_motifthree_quantile_size; i++) {
     fprintf(outfile, "%f, %s%i \n", out_ar[sb_motifthree_quantile[i]], "sb_motifthree_", i);
     }
     free(out_ar);
     
     
     memcpy(copy, y, size * sizeof *copy);
     out_ar = sb_motifthree(y, size, "diffquant");
     for (i = 0; i < sb_motifthree_diffquant_size; i++) {
     fprintf(outfile, "%f, %s%i \n", out_ar[sb_motifthree_diffquant[i]], "sb_motifthree", i);
     }
     free(out_ar);
     */
    
    fprintf(outfile, "\n");
    free(copy);
}

void print_help(char *argv[], char msg[])
{
    if (strlen(msg) > 0) {
        fprintf(stdout, "ERROR: %s\n", msg);
    }
    fprintf(stdout, "Usage is %s <infile> <outfile>\n", argv[0]);
    fprintf(stdout, "\n\tSpecifying outfile is optional, by default it is stdout\n");
    // fprintf(stdout, "\tOutput order is:\n%s\n", HEADER);
    exit(1);
}

int main(int argc, char *argv[])
{     
    FILE * infile, * outfile;
    int array_size;
    double * y;
    int size;
    double value;
    DIR *d;
    struct dirent *dir;
    
    switch (argc) {
        case 1:
            print_help(argv, "");
            break;
        case 2:
            if ((infile = fopen(argv[1], "r")) == NULL) {
                print_help(argv, "Can't open input file\n");
            }
            outfile = stdout;
            break;
        case 3:
            if ((infile = fopen(argv[1], "r")) == NULL) {
                print_help(argv, "Can't open input file\n");
            }
            if ((outfile = fopen(argv[2], "w")) == NULL) {
                print_help(argv, "Can't open output file\n");
            }
            break;
    }
    // fprintf(outfile, "%s", HEADER);
    array_size = 50;
    size = 0;
    y = malloc(array_size * sizeof *y);
    
    while (fscanf(infile, "%lf", &value) != EOF) {
        if (size == array_size) {
            y = realloc(y, 2 * array_size * sizeof *y);
            array_size *= 2;
        }
        y[size++] = value; 
    }
    fclose(infile);
    y = realloc(y, size * sizeof *y);
    run_features(y, size, outfile);
    fclose(outfile);
    free(y);
}
