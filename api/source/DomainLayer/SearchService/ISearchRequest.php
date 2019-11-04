<?php

namespace DomainLayer\SearchService;

interface ISearchRequest
{
    public function hasTerm();

    public function getTerm();

    public function hasCategory();

    public function getCategory();

    public function hasTag();

    public function getTag();

    public function hasSource();

    public function getSource();

    public function getPage();
}
