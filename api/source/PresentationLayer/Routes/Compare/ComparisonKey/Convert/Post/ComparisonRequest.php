<?php

namespace PresentationLayer\Routes\Compare\ComparisonKey\Convert\Post;

use DomainLayer\TimeSeriesManagement\Comparison\ComparisonService\IComparisonRequest;

class ComparisonRequest implements IComparisonRequest
{
    /**
     * @var string
     */
    private $comparisonKey;

    /**
     * @var boolean
     */
    private $shouldIgnoreTruncationWarning;

    /**
     * ComparisonRequest constructor.
     * @param $comparisonKey
     * @param $shouldIgnoreTruncationWarning
     */
    public function __construct($comparisonKey, $shouldIgnoreTruncationWarning)
    {
        $this->comparisonKey = $comparisonKey;
        $this->shouldIgnoreTruncationWarning = $shouldIgnoreTruncationWarning;
    }

    /**
     * @return string
     */
    public function getComparisonKey()
    {
        return $this->comparisonKey;
    }

    /**
     * @return bool
     */
    public function shouldIgnoreTruncationWarning()
    {
        return $this->shouldIgnoreTruncationWarning;
    }
}
