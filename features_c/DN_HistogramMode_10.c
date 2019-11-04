#include <math.h>
#include <string.h>
#include <time.h>
#include <float.h>
#include "stats.h"
#include "CO_AutoCorr.h"

double DN_HistogramMode_10(double y[], int size)
{
    
    double min = DBL_MAX, max=-DBL_MAX;
    for(int i = 0; i < size; i++)
    {
        if (y[i] < min)
        {
            min = y[i];
        }
        if (y[i] > max)
        {
            max = y[i];
        }
    }
    
    double binStep = (max - min)/10;
    
    // fprintf(stdout, "min=%f, max=%f, binStep=%f \n", min, max, binStep);
    
    int histCounts[10] = {0};
    for(int i = 0; i < size; i++)
    {
        int binsLeft = 10;
        int lowerInd = 0, upperInd = 10;
        while(binsLeft > 1)
        {
            int limitInd = (upperInd - lowerInd)/2 + lowerInd;
            double limit = limitInd * binStep + min;
            
            if (y[i] < limit)
            {
                upperInd = limitInd;
            }
            else
            {
                lowerInd = limitInd;
            }
            binsLeft = upperInd - lowerInd;
        }
        histCounts[lowerInd] += 1;
    }
    
    double maxCount = 0;
    int maxCountInd = 0;
    for(int i = 0; i < 10; i++)
    {
        // fprintf(stdout, "binInd=%i, binCount=%i \n", i, histCounts[i]);
        
        if (histCounts[i] > maxCount)
        {
            maxCountInd = i;
            maxCount = histCounts[i];
        }
    }
    return binStep*(maxCountInd+0.5) + min;
}
