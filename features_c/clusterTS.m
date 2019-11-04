cd 'featureOutputs'

files = dir('*.txt');
filenames = {files.name};
ids = cellfun(@(x) str2num(x(5:8)), filenames, 'UniformOutput', false);
ids = [ids{:}];

featureMat = [];

for fileInd = 1:numel(filenames)
   
    filename = filenames{fileInd}; 
    f = fopen(filename, 'r');
    
    C = textscan(f, '%f %s %f', 'Delimiter',',');
    
    featureMat = [featureMat, C{1}];
    
    fclose(f);
end

cd ..

% turn to have HCTSA convention
featureMat = featureMat';

% remove nans
featureMat(:,isnan(sum(featureMat,1))) = [];

% normalize
featureMatNorm = BF_NormalizeMatrix(featureMat, 'scaledRobustSigmoid');

% calculate distances between TS
reducedDists = pdist(featureMatNorm);

% Cluster the time series using the reduced (K-medoids) operation space
maxIter = 100;
av_ts_cluster_size = 100; % cluster count can't be more than nTS/nFeatures?
ts_km_repeats = 10;
ts_k = round(numel(filenames) / av_ts_cluster_size);

[~,Cass,~,Cord] = BF_kmedoids(squareform(reducedDists), ts_k, maxIter, ts_km_repeats); 

% Sort clusters by size
[~,I] = sort(cellfun(@length,Cord),'descend');

figure;
plotOptions	= struct('plotFreeForm',1,'displayTitles',0,'newFigure',0); 
numPerGroup = 25;
maxLength = 500;
numPlots = min(length(I),12);
for i = 1:numPlots
    subplot(2,numPlots / 2,i);
    series = find(Cass == I(i));

    TS_plot_timeseries('/Users/carl/PycharmProjects/DimRedHCTSA/Matlab/data/Empirical_realizations=1000__params/HCTSA.mat',numPerGroup,series,maxLength,plotOptions);
    title(sprintf('Cluster %i (%i time series)',i,length(series)));
end