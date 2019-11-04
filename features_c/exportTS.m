[~, TimeSeries, ~] = TS_LoadData('/Users/carl/PycharmProjects/DimRedHCTSA/Matlab/data/Empirical_realizations=1000__params/HCTSA.mat');

mkdir('exportedTS')
cd 'exportedTS'

for timeSeriesInd = 1:numel(TimeSeries)
    fprintf('timeSeriesInd=%i, timeSeriesID=%i\n', timeSeriesInd, TimeSeries(timeSeriesInd).ID);
    f = fopen(sprintf('tsid%04i.txt', TimeSeries(timeSeriesInd).ID), 'w');
    fprintf(f, "%6.10f\n", TimeSeries(timeSeriesInd).Data);
    fclose(f);
end

cd ..