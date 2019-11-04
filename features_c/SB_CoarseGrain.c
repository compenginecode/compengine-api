#include <math.h>
#include <string.h>
#include <stdlib.h>
#include <stdio.h>
#include "stats.h"
#include "helper_functions.h"

void sb_coarsegrain(double y[], int size, char how[], int num_groups, int labels[])
{   
    int i, j;
    if (strcmp(how, "quantile") == 1) {
        fprintf(stdout, "ERROR in sb_coarsegrain: unknown coarse-graining method\n");
        exit(1);
    }
    double * th = malloc((num_groups + 1) * sizeof(th));
    double * ls = malloc((num_groups + 1) * sizeof(th));
    linspace(0, 1, num_groups + 1, ls);
    for (i = 0; i < num_groups + 1; i++) {
        th[i] = quantile(y, size, ls[i]);
    }
    th[0] -= 1;
    for (i = 0; i < num_groups; i++) {
        for (j = 0; j < size; j++) {
            if (y[j] > th[i] && y[j] <= th[i + 1]) {
                labels[j] = i + 1;
            }
        }
    }
}