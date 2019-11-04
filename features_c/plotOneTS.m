function plotOneTS(fileInd)

    % now load TS and calculate Matlab features
    cd 'timeSeries'

    files = dir('*.txt');
    filenames = {files.name};
    idsTS = cellfun(@(x) str2num(x(5:8)), filenames, 'UniformOutput', false);
    idsTS = [idsTS{:}];

    featureMatMatlab = [];

    filename = filenames{fileInd}; 
    f = fopen(filename, 'r');

    TS = textscan(f, '%f');

    figure,
    plot(TS{1})
    title(filename);

    fclose(f);
    
    cd ..