function compareFeaturesMatlabC_calc_select(featureIndWanted)

    if nargin < 1 || isempty(featureIndWanted)
        featureIndWanted = 1;
    end
    
    featureNameSelection = { ...
            'CO_Embed2_Basic_tau.incircle_1', ...
            'CO_Embed2_Basic_tau.incircle_2', ...
            'FC_LocalSimple_mean1.taures', ...
            'SY_SpreadRandomLocal_ac2_100.meantaul', ...
            'DN_HistogramMode_10', ...
            'SY_StdNthDer_1', ...
            'AC_9', ...
            'SB_MotifTwo_mean.hhh', ... 
            'EN_SampEn_5_03.sampen1', ...
            'CO_FirstMin_ac', ... 'first_min_acf' replaced
            'DN_OutlierInclude_abs_001.mdrmd', ...
            'CO_trev_1.num', ...
            'FC_LocalSimple_lfit.taures' , ...
            'SY_SpreadRandomLocal_50_100.meantaul', ...
            'SC_FluctAnal_2_rsrangefit_50_1_logi.prop_r1', ...
            'PH_Walker_prop_01.sw_propcross', ...
            'PH_ForcePotential_sine_1_1_1.proppos', ...
            'SP_Summaries_pgram_hamm.maxw', ...
            'SP_Summaries_welch_rect.maxw', ...
            };
        
    featureNamesWanted = featureNameSelection(featureIndWanted);

    fprintf('Selected feature ''%s''\n', featureNamesWanted{1});
    
    cd 'featureOutputs'

    files = dir('*.txt');
    filenames = {files.name};
    idsOutput = cellfun(@(x) str2num(x(5:8)), filenames, 'UniformOutput', false);
    idsOutput = [idsOutput{:}];

    featureMat = [];

    for fileInd = 1:numel(filenames)

        filename = filenames{fileInd}; 
        f = fopen(filename, 'r');

        C = textscan(f, '%f %s %f', 'Delimiter',',');

        featureValsTemp = C{1};
        featureNamesTemp = C{2}; %cellfun(@(x) x(1:end-1), C{2}, 'UniformOutput', false);
        featureMat = [featureMat, featureValsTemp(ismember(featureNamesTemp,featureNamesWanted))];

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

        featureValMatlabTemp = calculateMatlabFeatures(TS{1}, featureNamesWanted);
        disp(featureValMatlabTemp);
        featureMatMatlab = [featureMatMatlab; featureValMatlabTemp];

        fclose(f);
    end

    cd ..

    % turn to have HCTSA convention
    featureMat = featureMat';
    
    % errors
    error = abs(featureMat - featureMatMatlab);
    % bigErrorInds = find(error > sqrt(std((featureMat.^2 + featureMatMatlab.^2)/2)));
    [~, sortedErrorInds] = sort(error, 'descend');
    bigErrorInds = sortedErrorInds(1:5);
    
    figure,
    scatter(featureMat, featureMatMatlab)
    hold on
    xLims = get(gca, 'xlim');
    plot(xLims, xLims, '--', 'Color', [1,1,1] * 0.7)
    xlabel('C feature');
    ylabel('Matlab feature')
    title(featureNamesWanted{1}, 'Interpreter', 'none');
    
    for i = bigErrorInds'
       text(featureMat(i), featureMatMatlab(i), filenames{i});
    end
    
end

function output = calculateMatlabFeatures(TS, featureNamesWanted)
        
    TS = (TS-mean(TS))/std(TS);

    if nargin<2 || isempty(featureNamesWanted)
        featureNamesWanted = { ...
            'CO_Embed2_Basic_tau.incircle_1', ...
            'CO_Embed2_Basic_tau.incircle_2', ...
            'FC_LocalSimple_mean1.taures', ...
            'SY_SpreadRandomLocal_ac2_100.meantaul', ...
            'DN_HistogramMode_10', ...
            'SY_StdNthDer_1', ...
            'AC_9', ...
            'SB_MotifTwo_mean.hhh', ... %'EN_SampEn_5_03.sampen1', ...
            'CO_FirstMin_ac', ... 'first_min_acf' replaced
            'DN_OutlierInclude_abs_001.mdrmd', ...
            'CO_trev_1.num', ...
            'FC_LocalSimple_lfit.taures', ...
            'SY_SpreadRandomLocal_50_100.meantaul', ...
            'SC_FluctAnal_2_rsrangefit_50_1_logi.prop_r1', ...
            'PH_Walker_prop_01_sw_propcross', ...
            'PH_ForcePotential_sine_1_1_1.proppos', ...
            'SP_Summaries_pgram_hamm.maxw', ...
            'SP_Summaries_welch_rect.maxw', ...
            };
    end

    output = [];
    
    resTemp = CO_Embed2_Basic(TS,'tau');
    if ismember('CO_Embed2_Basic_tau.incircle_1', featureNamesWanted)
        output = [output, resTemp.incircle_1];
    end
    if ismember('CO_Embed2_Basic_tau.incircle_2', featureNamesWanted)
        output = [output, resTemp.incircle_2];
    end
    
    if ismember('FC_LocalSimple_mean1.taures', featureNamesWanted)
        resTemp = FC_LocalSimple(TS,'mean',1);
        output = [output, resTemp.taures];
    end
    
    if ismember('SY_SpreadRandomLocal_ac2_100.meantaul', featureNamesWanted)
        resTemp = SY_SpreadRandomLocal(TS, 'ac2', 100);
        output = [output, resTemp.meantaul];
    end
    
    if ismember('DN_HistogramMode_10', featureNamesWanted)
        resTemp = DN_HistogramMode(TS, 10);
        output = [output, resTemp];
    end
    
    if ismember('SY_StdNthDer_1', featureNamesWanted)
        resTemp = SY_StdNthDer(TS, 1);
        output = [output, resTemp];
    end
    
    if ismember('AC_9', featureNamesWanted)
        resTemp = CO_AutoCorr(TS, 9);
        output = [output, resTemp];
    end
    
    if ismember('SB_MotifTwo_mean.hhh', featureNamesWanted)
        resTemp = SB_MotifTwo(TS, 'mean');
        output = [output, resTemp.hhh];
    end
    
    if ismember('EN_SampEn_5_03.sampen1', featureNamesWanted)
        resTemp = EN_SampEn(TS,5,0.3);
        output = [output, resTemp.sampen1];
    end
    
    if ismember('CO_FirstMin_ac', featureNamesWanted)
        resTemp = CO_FirstMin(TS, 'ac');
        output = [output, resTemp];
    end
    
    if ismember('DN_OutlierInclude_abs_001.mdrmd', featureNamesWanted)
        TSZscored = (TS-mean(TS))/std(TS);
        resTemp = DN_OutlierInclude(TSZscored, 'abs', 0.01);
        output = [output, resTemp.mdrmd];
    end
    
    if ismember('CO_trev_1.num', featureNamesWanted)
        resTemp = CO_trev(TS, 1);
        output = [output, resTemp.num];
    end
    
    if ismember('FC_LocalSimple_lfit.taures', featureNamesWanted)
        resTemp = FC_LocalSimple(TS, 'lfit', 'ac');
        output = [output, resTemp.taures];
    end
    
    if ismember('SY_SpreadRandomLocal_50_100.meantaul', featureNamesWanted)
        resTemp = SY_SpreadRandomLocal(TS, 50, 100);
        output = [output, resTemp.meantaul];
    end
    
    if ismember('SC_FluctAnal_2_rsrangefit_50_1_logi.prop_r1', featureNamesWanted)
        resTemp = SC_FluctAnal(TS,2,'rsrangefit',50,1,[],1);
        output = [output, resTemp.prop_r1];
    end
    
    if ismember('PH_Walker_prop_01.sw_propcross', featureNamesWanted)
        resTemp = PH_Walker(TS,'prop',0.1);
        output = [output, resTemp.sw_propcross];
    end
            
    
    
end

