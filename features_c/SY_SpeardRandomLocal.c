#include <math.h>
#include <string.h>
#include <stdio.h>
#include <time.h>
#include "stats.h"
#include "CO_AutoCorr.h"

double SY_SpreadRandomLocal_ac2_100_meantaul(double y[], int size, char randFlag)
{
    double l = 2*co_firstzero(y, size, size);
    
    int nSegs = 100; // number of randomly chosen signal pieces to analyse
    
    // if 'r', use a random seed
    if (randFlag == 'r')
        srand(time(0)); // reset seed
    else
        srand(1);
    
    double resArray[100];
    double * segTemp = malloc(l * sizeof *segTemp);
    for (int i = 0; i < nSegs; i++)
    {
        int startInd = rand() * (size - l)/RAND_MAX;
        
        // better hand incremented pointer y+startInd to co_firstzero
        for (int j = 0; j < l; j++)
        {
            segTemp[j] = y[startInd + j];
        }
        
        resArray[i] = co_firstzero(segTemp, l, l);
        
    }
    
    double output = mean(resArray, nSegs);
    
    free(segTemp);
    
    return output;
}


double SY_SpreadRandomLocal_50_100_meantaul(double y[], int size, char randFlag)
{
    double l = 50;
    int nSegs = 100; // number of randomly chosen signal pieces to analyse
    
    // if 'r', use a random seed
    if (randFlag == 'r')
        srand(time(0)); // reset seed
    else
        srand(1);
    
    double resArray[100];
    double * segTemp = malloc(l * sizeof *segTemp);
    for (int i = 0; i < nSegs; i++)
    {
        int startInd = rand() * (size - l)/RAND_MAX;
        
        // better hand incremented pointer y+startInd to co_firstzero
        for (int j = 0; j < l; j++)
        {
            segTemp[j] = y[startInd + j];
        }
        
        resArray[i] = co_firstzero(segTemp, l, l);
        
    }
    
    double output = mean(resArray, nSegs);
    
    free(segTemp);
    
    return output;
}
