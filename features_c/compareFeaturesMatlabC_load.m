function compareFeaturesMatlabC_load()

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
    'FC_LocalSimple_lfit.taures', 'FC_LocalSimple_lfittau.taures', ...
    'SY_SpreadRandomLocal_ac2_100.meantaul', ...
    'EN_SampEn_5_03.sampen1'};

    cd 'featureOutputs'

    files = dir('*.txt');
    filenames = {files.name};
    idsOutput = cellfun(@(x) str2num(x(5:8)), filenames, 'UniformOutput', false);
    idsOutput = [idsOutput{:}];

    featureMatC = [];

    for fileInd = 1:numel(filenames)

        filename = filenames{fileInd}; 
        f = fopen(filename, 'r');

        C = textscan(f, '%f %s %f', 'Delimiter',',');

        featureMatC = [featureMatC, C{1}(ismember(C{2}, wantedFeatureNames))];

        fclose(f);
    end

    cd ..

    featureMatC = featureMatC';
    
    % sort according to feature name
    [featureNamesC, sortInds] = sort(C{2});
    featureMatC = featureMatC(:, sortInds);
    
    
    % Load in data:
    [TS_DataMat,~,Operations] = TS_LoadData('/Users/carl/PycharmProjects/DimRedHCTSA/Matlab/data/Empirical_realizations=1000__params/fromCluster/HCTSA.mat'); % 'raw');

    OperationIDs = [Operations.ID];
    OperationNames = {Operations.CodeString};

    % indices of the wanted features
    wantedOpInds = find(cellfun(@(x) ismember(x, wantedFeatureNames), OperationNames));

    redOps = Operations(wantedOpInds);
    redOperationNames = OperationNames(wantedOpInds);

    % sort inds to align feature matrices
    [redOperationNames, sortInds] = sort(redOperationNames);
    wantedOpIndsSort = wantedOpInds(sortInds);
    
    % Create a reduced data matrix using the automatically chosen operations
    featureMatMatlab = TS_DataMat(:,wantedOpIndsSort);
    
    % compare the two matrices
    diffMat = abs(featureMatC-featureMatMatlab);
    
    featureErrorsMean = mean(diffMat, 1);
    featureErrorsStd = std(diffMat, [], 1);
    
    for featureInd = 1:length(featureErrorsMean)
        figure
        scatter(featureMatC(:,featureInd), featureMatMatlab(:, featureInd));
        xlabel(['C: ', featureNamesC{featureInd}], 'Interpreter', 'none');
        ylabel(['Matlab: ', redOperationNames{featureInd}], 'Interpreter', 'none');
%         disp('hm');
    end
    
%         figure;
%         errorbar(1:length(featureErrorsMean), featureErrorsMean, featureErrorsStd);
%         set(gca, 'yscale', 'log');
    
end

function output = calculateMatlabFeatures(TS)
        
%     wantedFeatureNames = { ...
%         'CO_Embed2_Basic_tau.incircle_1', ...
%         'CO_Embed2_Basic_tau.incircle_2', ...
%         'FC_LocalSimple_mean1.taures', ...
%         'SY_SpreadRandomLocal_ac2_100.meantaul', ...
%         'DN_HistogramMode_10', ...
%         'SY_StdNthDer_1', ...
%         'AC_9', ...
%         'SB_MotifTwo_mean.hhh', ...
%         'EN_SampEn_5_03.sampen1', ...
%         'CO_FirstMin_ac', ... 'first_min_acf' replaced
%         'DN_OutlierInclude_abs_001.mdrmd', ...
%         'CO_trev_1.num', ...
%         'FC_LocalSimple_lfittau.taures', ...
%         'SY_SpreadRandomLocal_50_100.meantaul', ...
%         'SC_FluctAnal_2_rsrangefit_50_1_logi.prop_r1', ...
%         'PH_ForcePotential_sine_1_1_1.proppos', ...
%         'SP_Summaries_pgram_hamm.maxw', ...
%         'SP_Summaries_welch_rect.maxw', ...
%         };

    output = [];
    
    resTemp = CO_Embed2_Basic(TS,'tau');
    output = [output, resTemp.incircle_1];
    output = [output, resTemp.incircle_2];
    
    resTemp = FC_LocalSimple(TS,'mean',1);
    output = [output, resTemp.taures];
    
    resTemp = SY_SpreadRandomLocal(TS, 'ac2', 100);
    output = [output, resTemp.meantaul];
    
    resTemp = DN_HistogramMode(TS, 10);
    output = [output, resTemp];
    
    resTemp = SY_StdNthDer(TS, 1);
    output = [output, resTemp];
    
    resTemp = CO_AutoCorr(TS, 9);
    output = [output, resTemp];
    
    resTemp = SB_MotifTwo(TS, 'mean');
    output = [output, resTemp.hhh];
    
    resTemp = CO_FirstMin(TS, 'ac');
    output = [output, resTemp];
    
    TSZscored = (TS-mean(TS))/std(TS);
    resTemp = DN_OutlierInclude(TSZscored, 'abs', 0.01);
    output = [output, resTemp.mdrmd];
    
    resTemp = CO_trev(TS, 1);
    output = [output, resTemp.num];
    
    resTemp = FC_LocalSimple(TS, 'lfit', 'ac');
    output = [output, resTemp.taures];
    
    resTemp = SY_SpreadRandomLocal(TS, 50, 100);
    output = [output, resTemp.meantaul];
    
end

