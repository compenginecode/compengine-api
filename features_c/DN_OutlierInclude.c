#include <math.h>
#include <string.h>
#include <time.h>
#include <float.h>
#include <stdio.h>
#include <stdlib.h>
#include "stats.h"

double DN_OutlierInclude_abs_001(double y[], int size)
{
    double inc = 0.01;
    double maxAbs = 0;
    double * yAbs = malloc(size * sizeof * yAbs);
    
    for(int i = 0; i < size; i++)
    {
        // yAbs[i] = (y[i] > 0) ? y[i] : -y[i];
        yAbs[i] = (y[i] > 0) ? y[i] : -y[i];
        
        if(yAbs[i] > maxAbs)
        {
            maxAbs = yAbs[i];
        }
    }
    
    int nThresh = maxAbs/inc + 1;
    
    // save the indices where y > threshold
    double * highInds = malloc(size * sizeof * highInds);
    
    // save the median over indices with absolute value > threshold
    double * msDti3 = malloc(nThresh * sizeof * msDti3);
    double * msDti4 = malloc(nThresh * sizeof * msDti4);

    for(int j = 0; j < nThresh; j++)
    {
        int highSize = 0;
        
        for(int i = 0; i < size; i++)
        {
            if(yAbs[i] >= j*inc)
            {
                // fprintf(stdout, "%i, ", i);
                
                highInds[highSize] = i;
                highSize += 1;
            }
        }
        
        // median
        double medianOut;
        medianOut = median(highInds, highSize);
        
        msDti3[j] = (highSize-1)*100.0/size;
        msDti4[j] = medianOut / (size/2) - 1;
        
    }
    
    int trimthr = 2;
    int mj = 0;
    for(int i = 0; i < nThresh; i ++)
    {
        if (msDti3[i] > trimthr)
        {
            mj = i;
        }
    }
    
    double outputScalar;
    outputScalar = median(msDti4, mj);

    free(highInds);
    free(yAbs);
    free(msDti4);
    
    return outputScalar;
}
