function [CCi, Cass, err, Cord, D] = BF_kmedoids(D, k, maxIter, nrepeats, errmeas, killer)
% Ben Fulcher 14/1/2011 -- want to input *just* a distance matrix, so no
% distance calculations are performed on the fly.

% Usage
% [IDX,C,cost] = kmedoid(data, NC, maxIter, [init_cluster])
%
% Input :
%       D: square distance matrix (e.g., use squareform on output of
%       'pdist')
%       k - number of clusters
%       maxIter - Maximum number of iterations
%       nrepeats - number of times to repeat the algorithm (with different
%               random initial cluster allocations)
%       errmeas [opt] - custom error measure
%
% Output :
%       CCi - indicies of cluster centre data points
%       CCass - assignments of each data point to a cluster (indicies of CCi)
%       err - sum of point-centroid distances for each cluster
%       Cord [opt] - k-component cell of cluster indicies, ordered in increasing
%           distance from centroids (first member of each cell component)
% ------------------------------------------

% Ben Fulcher 27/1/2011 added killer input -- does preliminary pruning of
%                       zero distances, adds them back at the end.

% ------------------------------------------------------------------------------
%% Check Inputs
% ------------------------------------------------------------------------------
if nargin < 2 || isempty(k)
    disp('I''m making two clusters and I don''t care who knows it');
    k = 2;
end
if nargin < 3 || isempty(maxIter)
    disp('I''m only doing 10 iterations. Don''t get angry -- you should have specified.')
    maxIter = 10;
end
if nargin < 4 || isempty(nrepeats)
    nrepeats = 10; % repeat to try and improve error
end
if nargin < 5 || isempty(errmeas)
    errmeas = 'sum'; % default: sum of distances to centroid
end
if nargin < 6 || isempty(killer)
    killer = 1;
end

%% Preliminary correction
if killer
    % % If D has any off-diagonal zeros, this can cause problems (empty clusters
    % % etc.). Also, it's nicer to remove these first as a trivial
    % % clustering, then add them back in at the end.
    [xi,xj] = find(D==0);
    diagi = find(xi==xj);
    xi(diagi)=[]; xj(diagi) = [];
    % what's left are off-diagonal entries
    lowert = find(xi<xj);
    xi(lowert) = []; xj(lowert) = [];
    % remove lower-trianglular entries (it's symmetric)
    % now we should have some number of entries that can cluster up. Keep those
    % with the lower index.
    nkill = length(xi);
    if nkill>0
        uxj = unique(xj);
        for i=1:length(uxj);
            r = find(xj==uxj(i));
            if sum(r)>1 % this entry has more than one d=0 with another
                xj(r(xi(r)>min(xi(r))))=[]; % keep only lowest index connection
            end
        end
        ikeep = setxor(1:length(D),xj);
        disp(['Off-Diagonal zeros: keeping ' num2str(length(ikeep)) ' of ' num2str(length(D)) ' for the distance matrix'])
        D0 = D; % copy original distances D as D0
        D = D(ikeep,ikeep);
        nkill = length(ikeep);
    %     D(xj,xj) = []; % remove these rows/columns for the purposes of clustering.
                       % bad for memory, but saves having to put an ugly index in every
                       % mention of D below.
    end
    % % nicer code-wise to just make a replicate D0 of D without these entries to
    % % run algorithm on, then at the end assign membership based on the original
    % % D. But we can just at the end at xjs to the xis that are chosen. So now,
    % % since we've killed intermediate connections (linking each to its lowest
    % % index if multiple connections), we can now just search for xis in the
    % % final outcome, and add xjs to each of these clusters
else
    nkill = 0;
end

% ------------------------------------------------------------------------------
%% Get Cracking
% ------------------------------------------------------------------------------
% preliminaries
l = length(D); % number of objects to cluster

% for nrepeats
CCiN = zeros(nrepeats,k);
errN = zeros(nrepeats,1); % sum of in-cluster distances (or whatever)

tic
for N = 1:nrepeats
    CCis = zeros(maxIter+1,k); % store cluster center indicies
%     errs = zeros(maxIter,k); % store errors

    % assign intial cluster centers
    rp = randperm(l);
    CCis(1,:) = sort(rp(1:k)); % pick k cluster centres at random from data

    for i = 1:maxIter
        % 1) minimize total error by assigning each observation to the nearest current
        % cluster centre
        [~,Cass] = min(D(CCis(i,:),:)); % minimum distance from each of the k cluster centers
        % cluster assignments Cass: vector (length l) of integers (1:k)

        % 2) for given cluster assignment find the observation in the cluster that
        % minimizes the total distance to other points in the cluster:
        for ik = 1:k
            Cki = find(Cass==ik); % cluster k indicies
            if isempty(Cki) % problem -- empty cluster because multiple zero distances
                % steal the cluster centre back
                disp('there must be off-diagonal zeros: stealing my cluster centre back into my cluster!!')
                Cass(CCis(i,ik)) = ik;
                Cki = CCis(i,ik); % could cause an error from previous iteration when assignment was different.
            end
            [~,imin] = min(sum(D(Cki,Cki)));
            CCis(i+1,ik) = Cki(imin);
        end
        CCis(i+1,:) = sort(CCis(i+1,:)); % just for aesthetic reasons
        if all(CCis(i,:)==CCis(i+1,:))
            CCis = CCis(1:i,:);
%             errs = errs(1:i,:);
            break
        end
    end
    CCiN(N,:) = CCis(end,:);

    % now compute some error function associated with this clustering:
    switch errmeas
        case 'sum'
            % minimize sum of within-cluster distances
            tmperrs = zeros(k,1);
            for ik = 1:k
                tmperrs(ik) = sum(D(CCiN(N,ik),Cass==ik)); % sum of distances in each cluster
            end
            errN(N) = sum(tmperrs); % sum of distances to cluster centre computed above
%                     errs(i,ik) = sum(D(Cki(imin),Cki));
        case 'medianall'
            % minimize median of *all* cluster-centroid distances
            tmperrs = cell(k,1);
            for ik = 1:k
                tmperrs(ik) = D(CCiN(N,ik),Cass==ik);
            end
            allerrs = vertcat(tmperrs{:});
            errN(N) = median(allerrs);
        case 'mediansum'
            % minimize sum of medians of cluster-centroid distances
            tmperrs = zeros(k,1);
            for ik = 1:k
                tmperrs(ik) = median(D(CCiN(N,ik),Cass==ik)); % median distance in each cluster
            end
            errN(N) = sum(tmperrs); % sum of distances to cluster centre computed above
        case 'maxsum'
            % (want to minimize) sum of maximum cluster-centroid distances
            tmperrs = zeros(k,1);
            for ik = 1:k
                tmperrs(ik) = max(D(CCiN(N,ik),Cass==ik)); % median distance in each cluster
            end
            errN(N) = sum(tmperrs); % sum of distances to cluster centre computed above
        case 'maxall'
            % (want to minimize) maximum of *all* cluster-centroid distances
            tmperrs = cell(k,1);
            for ik = 1:k
                tmperrs(ik) = D(CCiN(N,ik),Cass==ik);
            end
            allerrs = vertcat(tmperrs{:});
            errN(N) = max(allerrs);
    end

%     errN(N,:) = errs(end,:);
    if i==maxIter, disp('DIDN''T CONVERGE :(');
    else
        if N>1 && errN(N) == min(errN(1:N)) % new minimum error -- best yet
            disp(['[' num2str(N) '/' num2str(nrepeats) '] Converged at ' num2str(i) '/' num2str(maxIter) ' -- Err = '...
                num2str(errN(N)) ' (' num2str(errN(N)-min(errN(1:N-1))) ')']);
        else % worse clustering than best so far -- by how much?
            disp(['[' num2str(N) '/' num2str(nrepeats) '] Converged at ' num2str(i) '/' num2str(maxIter) ' -- Err = '...
                num2str(errN(N)) ' (+' num2str(errN(N)-min(errN(1:N))) ')']);
        end
    end
end

% ------------------------------------------------------------------------------
%% Polishing
% ------------------------------------------------------------------------------
if N==1
    CCi = CCiN(1,:);
    err = errN(1,:);
else
    % which repeat had minimum error
    sumerrN = sum(errN,2);
    [~,thebest] = min(sumerrN);
    CCi = CCiN(thebest,:);
    err = errN(thebest,:);
end

% add initially-deleted d=0 links if nkill>0
if nkill > 0
    % CCi actually indicies of ikeep
    CCi = ikeep(CCi);
    % need to assign based on distances of original points (D0)
    % to chosen cluster centres, in fact all the rest of the analysis
    % should be on D0 with the mapped CCis:
    D = D0;
end

% Assign to clusters using best partition:
[~,Cass] = min(D(CCi,:)); % minimum distance from each of the k cluster centers

% make sure each cluster centre is in its cluster
if ~all(Cass(CCi) == 1:k);
    % might actually want to disallow these clusterings
    disp('reassigning ambiguous/redundant clustering');
    Cass(CCi) = (1:k);
end

% Compute a nice, ordered clustering output
if nargout>=4
    Cord = cell(k,1);
    for ik = 1:k
        ciki = find(Cass==ik);
        [~,ix] = sort(D(CCi(ik),ciki),'ascend');
        Cord{ik} = ciki(ix);
    end
end

fprintf(1,'**BF_kmedoids took %s  to compute %u iterations on %u objects\n',...
                BF_thetime(toc),nrepeats,l)

end
