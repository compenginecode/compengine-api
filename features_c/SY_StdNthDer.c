#include <math.h>
#include <stdio.h>
#include "stats.h"

double SY_StdNthDer(double y[], int size, int n)
{
    
    for (int i = 0; i < n; i++)
    {
        for(int j = 0; j < size-1; j++)
        {
            double buffer = y[j+1] - y[j];
            y[j] = buffer;
        }
        size -= 1;
    }
    
    double out = stddev(y, size);
    return out;
}
