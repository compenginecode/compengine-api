% Load in data:
HCTSA_loc = '/Users/carl/PycharmProjects/DimRedHCTSA/Matlab/data/Empirical_realizations=1000__params/fromCluster/HCTSA_N.mat';
[TS_DataMat,~,Operations] = TS_LoadData(HCTSA_loc);

% calculate distances between TS
dists = pdist(TS_DataMat); % TS_DataMat); %

% those features are now sorted by mean execution time over the 1000
% empirical TS
wantedFeatureNames = {...
    'PH_Walker_prop_01.sw_propcross', ...          
    'CO_trev_1.num', ...        
    'DN_HistogramMode_10', ...
    'SY_StdNthDer_1', ...
    'SB_MotifTwo_mean.hhh', ...
    'SY_SpreadRandomLocal_50_100.meantaul', ...
    'SC_FluctAnal_2_rsrangefit_50_1_logi.prop_r1', ...
    'AC_9', ...
    'FC_LocalSimple_mean1.taures', ...
    'CO_FirstMin_ac', ...
    'CO_Embed2_Basic_tau.incircle_2', ...
    'CO_Embed2_Basic_tau.incircle_1', ...       
    'DN_OutlierInclude_abs_001.mdrmd', ...        
    'FC_LocalSimple_lfit.taures', ...
    'SY_SpreadRandomLocal_ac2_100.meantaul', ...
    'EN_SampEn_5_03.sampen1'};


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
    
    featureNames = C{2};
    featureMat = [featureMat, C{1}(ismember(featureNames, wantedFeatureNames))];
    
    fclose(f);
end

cd ..

% turn to have HCTSA convention
featureMat = featureMat';

% % remove nans
% featureMat(:,isnan(sum(featureMat,1))) = [];

% normalize
featureMatNorm = BF_NormalizeMatrix(featureMat, 'scaledRobustSigmoid');

errors = ones(numel(featureNames), 1);
for i = 1:numel(featureNames)
    
    featureMatNormSelect = featureMatNorm(:,ismember(featureNames, wantedFeatureNames(1:i)));
    
    n = size(featureMatNormSelect, 2);
    w0 = ones(1,n);

    % fun = @(w) sum(sum(abs(pdist(featureMatNormSelect.*repmat(w,size(featureMatNormSelect,1),1)) - dists)));
    fun = @(w) sum(sum(abs(pdist(featureMatNormSelect.*repmat(abs(w),size(featureMatNormSelect,1),1)) - dists)));

    options = optimset('PlotFcns',@optimplotfval);
    [w, err] = fminsearch(fun,w0,options)

    errors(i) = err;

end

figure
plot(errors, 'LineWidth', 2)
xlabel('num features')
ylabel('error (sum of absolute differences in distances between TS)');

% % calculate distances between TS
% reducedDists = pdist(featureMatNormSelect.*repmat(w,size(featureMatNormSelect,1),1)); % TS_DataMat); % 

% % calculate distances between TS
% reducedDists = pdist(featureMatNorm);

% % Cluster the time series using the reduced (K-medoids) operation space
% maxIter = 100;
% av_ts_cluster_size = 100; % cluster count can't be more than nTS/nFeatures?
% ts_km_repeats = 10;
% ts_k = round(numel(filenames) / av_ts_cluster_size);
% 
% [~,Cass,~,Cord] = BF_kmedoids(squareform(reducedDists), ts_k, maxIter, ts_km_repeats); 
% 
% % Sort clusters by size
% [~,I] = sort(cellfun(@length,Cord),'descend');
% 
% figure;
% plotOptions	= struct('plotFreeForm',1,'displayTitles',0,'newFigure',0); 
% numPerGroup = 25;
% maxLength = 500;
% numPlots = min(length(I),12);
% for i = 1:numPlots
%     subplot(2,numPlots / 2,i);
%     series = find(Cass == I(i));
% 
%     TS_plot_timeseries('/Users/carl/PycharmProjects/DimRedHCTSA/Matlab/data/Empirical_realizations=1000__params/HCTSA.mat',numPerGroup,series,maxLength,plotOptions);
%     title(sprintf('Cluster %i (%i time series)',i,length(series)));
% end