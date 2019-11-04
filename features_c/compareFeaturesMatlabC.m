function compareFeaturesMatlabC()

    cd 'featureOutputs'

    files = dir('*.txt');
    filenames = {files.name};
    idsOutput = cellfun(@(x) str2num(x(5:8)), filenames, 'UniformOutput', false);
    idsOutput = [idsOutput{:}];

    featureMat = [];

    for fileInd = 1:numel(filenames)

        filename = filenames{fileInd}; 
        f = fopen(filename, 'r');

        C = textscan(f, '%f %s', 'Delimiter',',');

        featureMat = [featureMat, C{1}];

        fclose(f);
    end

    cd ..

    % now load TS and calculate Matlab features
    cd 'timeSeries'

    files = dir('*.txt');
    filenames = {files.name};
    idsTS = cellfun(@(x) str2num(x(5:8)), filenames, 'UniformOutput', false);
    idsTS = [idsTS{:}];

    featureMatMatlab = [];

    for fileInd = 1:numel(filenames)

        fprintf('fileInd = %i\n', fileInd);
        filename = filenames{fileInd}; 
        f = fopen(filename, 'r');

        TS = textscan(f, '%f');

        featureMatMatlab = [featureMatMatlab; calculateMatlabFeatures(TS{1})];

        fclose(f);
    end

    cd ..

    % turn to have HCTSA convention
    featureMat = featureMat';
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

