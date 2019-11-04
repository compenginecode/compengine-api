# features_c
Some of the time series features of [HCTSA](https://github.com/benfulcher/hctsa) coded in C

# Compilation

## OS X:
gcc -o run_features run_features.c FC_LocalSimple.c SY_SlidingWindow.c PH_Walker.c SY_StdNthDer.c CO_AutoCorr.c stats.c fft.c FC_LocalSimple_ac.c SB_MotifTwo.c helper_functions.c SB_CoarseGrain.c EN_SampEn.c SY_SpeardRandomLocal.c DN_HistogramMode_10.c SB_MotifThree.c DN_OutlierInclude.c SC_FluctAnal.c

## Ubuntu:
gcc -o run_features run_features.c FC_LocalSimple.c SY_SlidingWindow.c -lm PH_Walker.c SY_StdNthDer.c CO_AutoCorr.c stats.c -lm fft.c FC_LocalSimple_ac.c SB_MotifTwo.c helper_functions.c SB_CoarseGrain.c -lm EN_SampEn.c -lm SY_SpeardRandomLocal.c -lm DN_HistogramMode_10.c -lm SB_MotifThree.c -lm DN_OutlierInclude.c -lm SC_FluctAnal.c

# Usage

## Single files

The compiled `run_features` program only takes one time series at a time. Usage is `./run_features <infile> <outfile>` in the terminal, where specifying `<outfile>` is optional, it prints to  `stdout` by default.

## Mutliple files

For multiple time series, put them – one file for each – into a folder `timeSeries` and call `./runAllTS.sh`. The output will be written into a folder `featureOutput`.

## Output format

Each line of the output correponds to one feature; the three comma-separated entries per line correspond to feature value, feature name and feature execution time in milliseconds. E.g.
```
0.29910714285714, CO_Embed2_Basic_tau.incircle_1, 0.341000
0.57589285714286, CO_Embed2_Basic_tau.incircle_2, 0.296000
...
```

## Clustering

Once the batch job on multiple files has been executed (`runAllTS.sh`), the Matlab script `clusterTS.m` can be called which will cluster the time series and display them. It assumes the feature output files in folder `featureOutput` has been calculated on the time series in the folder `timeSeries`.

## Fastest features

If execution time needs to be <1s for time series of length 50,000 samples, the last three features in `run_features.c` (see comment) can be removed.

## Testing

Sample outputs for the time series `test.txt` and `test2.txt` are provided as `test_output.txt` and `test2_output.txt`. The first two entries per line should always be the same. The third one (execution time) will be different.
