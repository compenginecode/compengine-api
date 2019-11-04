function sortedFeatures = featureExecTime_sort()
        
    cd 'featureOutputs'

    files = dir('*.txt');
    filenames = {files.name};
    idsOutput = cellfun(@(x) str2num(x(5:8)), filenames, 'UniformOutput', false);
    idsOutput = [idsOutput{:}];

    featureMat = [];
    timeMat = [];

    for fileInd = 1:numel(filenames)

        filename = filenames{fileInd}; 
        f = fopen(filename, 'r');

        C = textscan(f, '%f %s %f', 'Delimiter',',');

        featureValsTemp = C{1};
        featureNamesTemp = C{2}; % cellfun(@(x) x(1:end-1), C{2}, 'UniformOutput', false);
        timeTakenTemp = C{3};
        
        featureMat = [featureMat, featureValsTemp];
        timeMat = [timeMat, timeTakenTemp];

        fclose(f);
    end

    cd ..
    
    % turn to have HCTSA convention
    featureMat = featureMat';
    timeMat = timeMat';

    meanTimes = mean(timeMat,1);
    stdTimes = std(timeMat,1);
    
    [meanTimeSorted, sortInds] = sort(meanTimes, 'ascend');
    stdTimesSorted = stdTimes(sortInds);
    
    fprintf("\nSorted features: \n")
    for i = 1:length(sortInds)
        fprintf("%3.1fms +/- %3.1fms, %s\n", meanTimeSorted(i), stdTimesSorted(i), featureNamesTemp{sortInds(i)});
    end
    

    sortedFeatures = featureNamesTemp(sortInds);
    
end

