<?php

namespace DomainLayer\TimeSeriesManagement\Comparison\ComparisonService;


interface IComparisonRequest
{
    public function getComparisonKey();

    public function shouldIgnoreTruncationWarning();
}
