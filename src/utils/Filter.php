<?php

namespace Src\Utils;

class Filter
{
    private $availableFilters;
    private $exceptions;
    function __construct(...$availableFilters)
    {
        $this->availableFilters = $availableFilters;
        $this->exceptions = ["page", "limit", "sort", "order"];
    }


    private function checkFilters($filters)
    {
        if (count($this->availableFilters) < 1) {
            return "No filters available";
        }
        $unavailableFilters = [];
        foreach ($filters as $filter) {
            if (!in_array($filter, $this->availableFilters) && !in_array($filter, $this->exceptions)) {
                $unavailableFilters[] = $filter;
            }
        }
        $unavailableFiltersToString = implode(", ", $unavailableFilters);
        return count($unavailableFilters) > 0 ? "filters unavailable ($unavailableFiltersToString)" : true;
    }
    function getFilterStr()
    {
        $filter_keys = array_keys($_GET);

        $isFiltersAvailable = $this->checkFilters($filter_keys);
        if ($isFiltersAvailable !== true) {
            return $isFiltersAvailable;
        }

        $filter_values = array_values($_GET);

        if (array_search("", $filter_values) !== false) {
            return "filter values cannot be empty";
        }

        $filters = $_GET;
        
        foreach($this->exceptions as $exception){
            unset($filters[$exception]);
        }

        $filterStr = $this->filterStringBuilder($filters);


        return $filterStr;
    }

    private function filterStringBuilder($filters)
    {
        $filterArray = [];

        foreach ($filters as $key => $value) {
            array_push($filterArray, "$key=$value");
        }

        return implode(" AND ", $filterArray);
    }


    function getAvailableFilters()
    {
        return $this->availableFilters;
    }
}




/* 
$_GET -> "user_id" 


*/