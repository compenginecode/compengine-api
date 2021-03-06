<?php

require_once "source/bootstrap.php";

global $container;
global $entityManager;

$reporter = function($message){
    echo " - " . $message . PHP_EOL;
};

/** @var \Install\DatabaseInstaller\DatabaseInstaller $databaseInstaller */
$databaseInstaller = $container->get("Install\\DatabaseInstaller\\DatabaseInstaller");
$databaseInstaller->installDatabase($reporter);

$categories = array(
    "Real" => array(
        "Astrophysics" => array(
            "Light Curve" => [],
        ),
        "Audio" => array(
            "Animal sounds" => [],
            "Human speach" => [],
            "Music" => [],
            "Sound effects" => [],
        ),
        "Cognitive science" => array(
            "Human recall" => [],
        ),
        "Finance" => array(
            "Exchange rate" => [],
            "High low" => [],
            "Oil stocks" => [],
            "Opening prices" => [],
        )
    ),
    "Synthetic" => array(
        "Cao torus" => [],
        "Flow" => array(
            "ACT attractor" => [],
            "Chen's system" => [],
            "Chua's circuit" => [],
        )
    )
);

function addCategory($parent, $childrenArray){
    global $entityManager;

    foreach($childrenArray as $aChildName => $childrenChildren){
        $category = new \DomainLayer\ORM\Category\Category($aChildName, $parent);
        $category->setApprovalStatus(\DomainLayer\ORM\ApprovalStatus\ApprovalStatus::approved());
        $entityManager->persist($category);

        if (is_array($childrenChildren) && count($childrenChildren) > 0) {
            addCategory($category, $childrenChildren);
        }
    }
}

addCategory(NULL, $categories);


$tags = array(
    "raw",
    "lengthdep",
    "burstiness",
    "locdep",
    "distribution",
    "location",
    "spreaddep",
    "spread",
    "diff",
    "moment",
    "shape",
    "cv",
    "skewness",
    "noisiness",
    "correlation",
    "autocorrelation",
    "tau",
    "nonlinearautocorr",
    "information",
    "AMI",
    "glscf",
    "periodicity",
    "stationarity",
    "StatAv",
    "slidingwin",
    "dfa",
    "scaling",
    "mex",
    "hurstexp",
    "entropy",
    "shannon",
    "shannonpdf",
    "logenergy",
    "threshold",
    "sure",
    "binary",
    "fit",
    "gof",
    "ksdensity",
    "numpeaks",
    "peakmax",
    "area",
    "symmetry",
    "arclength",
    "crossconst",
    "areaconst",
    "compare",
    "max",
    "silly",
    "randomnumber",
    "trevasym",
    "nonlinearity",
    "hypothesistest",
    "signtest",
    "randomness",
    "runstest",
    "lbq",
    "econometricstoolbox",
    "ztest",
    "signrank",
    "jbtest",
    "chi2gof",
    "ks",
    "lillie",
    "determinism",
    "kaplan",
    "tstool",
    "dimension",
    "takens",
    "crptool",
    "outliers",
    "trend",
    "wavelet",
    "waveletTB",
    "varchg",
    "MichaelSmall",
    "complexity",
    "LempelZiv",
    "transitionmat",
    "constant",
    "spline",
    "dblwell",
    "embedding",
    "visibilitygraph",
    "sampen",
    "controlen",
    "slow",
    "forecasting",
    "gauss1",
    "gauss2",
    "exp1",
    "power1",
    "norm",
    "adiff",
    "peaksepy",
    "peaksepx",
    "olapint",
    "relent",
    "uni",
    "lognormal",
    "motifs",
    "model",
    "var",
    "r2",
    "sin1",
    "adjr2",
    "rmse",
    "resAC1",
    "resAC2",
    "resruns",
    "sin2",
    "sin3",
    "fourier1",
    "fa",
    "lini",
    "rsrange",
    "powerspectrum",
    "cepstrum",
    "tisean",
    "acp",
    "cao",
    "corrdim",
    "corrsum",
    "corrsum2",
    "localdensity",
    "poincare",
    "largelyap",
    "returntime",
    "surrogate",
    "preprocessing",
    "gaussianprocess",
    "cwt",
    "statTB",
    "dwt",
    "modelfit",
    "arfit",
    "fnn",
    "nlpe",
    "tdembedding",
    "pca",
    "systemidentificationtoolbox",
    "prediction",
    "statespace",
    "arma",
    "siddarth",
    "expsmoothing",
    "garch",
    "aic",
    "bic",
    "vratiotest",
    "pvalue",
    "pptest",
    "unitroot",
    "hqc",
    "kpsstest",
    "gharamani",
    "hmm",
    "stepdetection",
    "kalafutvisscher",
    "l1pwc",
    "statistics",
    "surrogatedata",
    "misc",
    "beta",
    "ev",
    "exp",
    "gamma",
    "rayleigh",
    "weibull",
    "nonlinear",
    "stochastic",
    "maxlittle",
    "medical",
    "symbolic",
    "AR",
    "network",
    "dynsys",
    "sine"
);

foreach($tags as $aTagName){
    $tag = new \DomainLayer\ORM\Tag\Tag($aTagName, \DomainLayer\ORM\ApprovalStatus\ApprovalStatus::approved());
    $entityManager->persist($tag);
}

$sources = array(
    "2002 Physionet Challenge",
    "Andreas S. Weigend Time Series Data",
    "LeBaron Exchange Rates",
    "Assymetric Logistic map simple",
    "Beta noise Matlab",
    "Binomial noise Matlab",
    "Cao's Periodic Map Ben",
    "Chi-squared noise Matlab",
    "Climatic Research Unit, University of East Anglia",
    "CRU UK Meteorology",
    "Japanese meteorology Zaiki, CRU",
    "Lamb/Jenkins Weather Types, CRU",
    "Madras Monthly Sea Level, CRU",
    "Mediterranian Oscillation Index (MOI), CRU",
    "NAO (North Atlantic Oscillation), CRU",
    "NAO Modifications, CRU",
    "NAO Reconstructions, CRU",
    "NCEP/NCAR, CRU",
    "Air Temperature, NCEP/NCAR, CRU",
    "Hawaii Ocean Time-series Data Organization & Graphical System",
    "Precipitation rate, NCEP/NCAR, CRU",
    "Relative humidity, NCEP/NCAR, CRU",
    "Sea level pressure, NCEP/NCAR, CRU",
    "North Sea Caspian Pattern (NCP), CRU",
    "Southern Oscillation Index (SOI), CRU",
    "Trans Polar Index (TPI), CRU",
    "Continuous uniform noise Matlab",
    "Discrete uniform noise Matlab",
    "Driven harmonic oscillator (BF)",
    "Driven pendulum (BF)",
    "Duffing (BF)",
    "Eric Weeks",
    "Exponential noise Matlab",
    "Exponential noise simple",
    "Extreme value noise Matlab",
    "F noise Matlab",
    "Financial log returns (BF)",
    "Frietas Stochastic Sine Map Ben",
    "Gamma noise Matlab",
    "Gamma noise simple",
    "Gaussian noise Matlab",
    "Gaussian noise simple",
    "Generalized Extreme Value noise Matlab",
    "Generalized Pareto random numbers Matlab",
    "Geometric noise Matlab",
    "Google trends",
    "Henon map simple",
    "Hypergeometric noise Matlab",
    "iTunes (BF)",
    "Downsampled music (BF)",
    "Downsampled podcasts (BF)",
    "Music snippets (BF)",
    "Podcast snippets (BF)",
    "Jerk 1 simulations (BF)",
    "Jerk 2 simulations (BF)",
    "Log-Normal noise Matlab",
    "Logistic Map A sweep (BF)",
    "Logistic map simple",
    "Lorenz system (BF)",
    "Lozi map simple",
    "Macaulay Library",
    "Malia Mason Recall Data",
    "Matlab simulated (BF)",
    "Noisy sine dataset (BF)",
    "Nonlinear observation autoregressive",
    "Physionet",
    "Physionet: Gait Mature",
    "Physionet: Gait PDB",
    "Physionet: NESFDB",
    "Physionet: neuro-degenerative gait",
    "Physionet: Tremor DB",
    "Physionet RR CHF NSR",
    "Physionet: CHFDB",
    "Physionet: MGHDB",
    "Physionet: NSRDB",
    "Poisson noise Matlab",
    "Poisson noise simple",
    "Powernoise simulated (BF)",
    "Processed archives of literature and text (BF)",
    "Literature.org",
    "Project Gutenberg",
    "Random coefficient simulations of AR processes (BF)",
    "Rayleigh noise Matlab",
    "rmpnoise simulations (BF)",
    "Santa Fé Time Series Competition",
    "Santa Fé Time Series Competition: Dataset A",
    "Santa Fé Time Series Competition: Dataset B",
    "Santa Fé Time Series Competition: Dataset C",
    "Santa Fé Time Series Competition: Dataset D",
    "Santa Fé Time Series Competition: Dataset E",
    "Santa Fé Time Series Competition: Dataset F",
    "SDE Toolbox Simulated",
    "SDE Toolbox M10a",
    "SDE Toolbox M1a",
    "SDE Toolbox M2a",
    "SDE Toolbox M3a",
    "SDE Toolbox M5a",
    "SDE Toolbox M6a",
    "SDE Toolbox M7a",
    "SDE Toolbox M8a",
    "Seismic Shumway Stoffer",
    "Shumway Stoffer Time Series Analysis and Its Applications 2nd Ed",
    "Simulations of approximately MIX(P) processes (BF)",
    "Simulations of Faes nonlinear AR process (BF)",
    "Simulations of Freitas nonlinear moving average filter (BF)",
    "Simulations of MA processes (BF)",
    "Simulations of Rossler system (BF)",
    "Sound Jay",
    "Sound Jay button sounds",
    "Sound Jay communication sounds",
    "Sound Jay household sounds",
    "Sound Jay human sounds",
    "Sound Jay mechanical sounds",
    "Sound Jay nature sounds",
    "Sound Jay transportation sounds",
    "SPIDR",
    "SPIDR Geomagnetic",
    "SPIDR Geomagnetic annual means — Ionosphere",
    "SPIDR HPI DMSP",
    "SPIDR HPI NOAA",
    "SPIDR Interplanetary Magnetic Field",
    "SPIDR Radio Solar Telescope Network",
    "SPIDR Solar Data",
    "SPIDR VOSTOK",
    "Sprott 3D Flow A",
    "Sprott 3D Flow B",
    "Sprott 3D Flow C",
    "Sprott 3D Flow D",
    "Sprott 3D Flow E",
    "Sprott 3D Flow F",
    "Sprott 3D Flow G",
    "Sprott 3D Flow H",
    "Sprott 3D Flow I",
    "Sprott 3D Flow J",
    "Sprott 3D Flow K",
    "Sprott 3D Flow L",
    "Sprott 3D Flow M",
    "Sprott 3D Flow N",
    "Sprott 3D Flow O",
    "Sprott 3D Flow P",
    "Sprott 3D Flow Q",
    "Sprott 3D Flow R",
    "Sprott 3D Flow S",
    "Sprott ACT attractor",
    "Sprott Chen's system",
    "Sprott Chua's circuit",
    "Sprott Complex Butterfly",
    "Sprott Conservative Flows",
    "Sprott Driven Pendulum",
    "Sprott Hénon-Heiles System",
    "Sprott Labyrinth Chaos",
    "Sprott Nosé-Hoover Oscillator",
    "Sprott simplest driven chaotic flow",
    "Sprott Conservative Maps",
    "Sprott Arnold's Cat Map",
    "Sprott Chaotic Web Map",
    "Sprott Chirikov Map",
    "Sprott Gingerbreadman Map",
    "Sprott Henon area-preserving quadratic map",
    "Sprott Lorenz 3D chaotic map",
    "Sprott Damped driven pendulum",
    "Sprott Diffusionless Lorenz Attractor",
    "Sprott Dissipative Maps",
    "Sprott Burgers map",
    "Sprott Delayed Logistic Map",
    "Sprott Discrete Predator-Prey Map",
    "Sprott Dissipative Standard Map",
    "Sprott Henon Map",
    "Sprott Holmes cubic map",
    "Sprott Ikeda Map",
    "Sprott Kaplan Yorke map",
    "Sprott Lozi Map",
    "Sprott Sinai Map",
    "Sprott Tinkerbell map",
    "Sprott Double Scroll",
    "Sprott Driven van der Pol oscillator",
    "Sprott Duffing two-well oscillator",
    "Sprott Duffing-van der Pol Oscillator",
    "Sprott Forced Brusselator",
    "Sprott Hadley circulation",
    "Sprott Halvorsen's cyclically symmetric attractor",
    "Sprott Jerk Systems",
    "Sprott Lorenz Attractor",
    "Sprott Moore-Spiegel oscillator",
    "Sprott Noninvertible Maps",
    "Sprott Cubic Map",
    "Sprott Cusp map",
    "Sprott Gauss map",
    "Sprott Gaussian white chaotic map",
    "Sprott linear congruential generator",
    "Sprott Logistic Map",
    "Sprott Pinchers Map",
    "Sprott Ricker",
    "Sprott Sine-Circle Map",
    "Sprott Spence Map",
    "Sprott Tent Map",
    "Sprott Rössler Attractor",
    "Sprott Rabinovich-Fabrikant attractor",
    "Sprott Rayleigh-Duffing Oscillator",
    "Sprott Rucklidge attractor",
    "Sprott Shaw-van der Pol oscillator",
    "Sprott simplest cubic chaotic flow",
    "Sprott Simplest piecewise linear chaotic flow",
    "Sprott simplest quadratic chaotic flow",
    "Sprott Thomas' cyclically symmetric attractor",
    "Sprott Ueda Oscillator",
    "Sprott WINDMI attractor",
    "StatLib",
    "Synthetic Physionet data",
    "T noise Matlab",
    "Time-Series Data Library",
    "Timmer nonstationary autoregressive processes",
    "Tinkerbell map simple",
    "Uniform noise simple",
    "US Energy Information Administration",
    "NYMEX Futures Prices",
    "US Oil Refinery Stocks",
    "US Oil Retail Gasoline and Diesel Prices",
    "World Crude Oil Prices",
    "Van der Pol oscillator (BF)",
    "Weibull noise Matlab",
    "Yahoo Finance",
    "Yahoo Finance Sectors",
    "Yahoo Finance Shares"
);

foreach($sources as $aSourceName){
    $source = new \DomainLayer\ORM\Source\Source($aSourceName);
    $source->setApprovalStatus(\DomainLayer\ORM\ApprovalStatus\ApprovalStatus::approved());
    $entityManager->persist($source);
}

$entityManager->flush();

$staticPageList = new \DomainLayer\ORM\SiteAttribute\SiteAttribute("staticPageList", "[\"/#!/about\"]");
$entityManager->persist($staticPageList);
$entityManager->flush();

$comparisonResultCacheTime = new \DomainLayer\ORM\SiteAttribute\SiteAttribute("comparisonResultCacheTime", 24*60*60);
$entityManager->persist($comparisonResultCacheTime);
$entityManager->flush();

/** Delete old indices first */
$reporter("Deleting old indices");
global $elasticSearch;
foreach(array_keys($elasticSearch->indices()->getAliases()) as $anIndexKey){
    $elasticSearch->indices()->delete(array(
        "index" => $anIndexKey
    ));
}

$lshOptions = new \DomainLayer\ORM\LSHOptions\LSHOptions(5, 7);
/** @var \DomainLayer\TimeSeriesManagement\Comparison\Family\CreateFamilyService $createFamilyService */
$createFamilyService = $container->get("DomainLayer\\TimeSeriesManagement\\Comparison\\Family\\CreateFamilyService");
$featureVectorFamily = $createFamilyService->createNewFeatureVectorFamily(
    "Test Feature Vector Family",
    "Used for development purposes",
    "php v:\\vokke\\imperial-college-api\\api\\private\\processes\\feature-vector-generator\\test-generator.php",
    $lshOptions,
    $lshOptions,
    $lshOptions
);

$entityManager->persist($featureVectorFamily);
$entityManager->flush();
$entityManager->refresh($featureVectorFamily);

$currentFeatureVectorFamily = new \DomainLayer\ORM\SiteAttribute\SiteAttribute("currentFeatureVectorFamily", $featureVectorFamily->getId());
$entityManager->persist($currentFeatureVectorFamily);
$entityManager->flush();

$reporter("Done");