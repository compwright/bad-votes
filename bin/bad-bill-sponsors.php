<?php

use Compwright\BadVotes\BadBills\WorksheetCollection;
use Compwright\BadVotes\OpenStatesDataProvider;
use Compwright\BadVotes\Reports\BadBillSponsors;

require dirname(__DIR__) . '/vendor/autoload.php';

ini_set('memory_limit', '512M');

$worksheets = WorksheetCollection::readFromCsv(
    dirname(__DIR__) . '/resources/data/' . 'SC_2023-2024_worksheets.csv'
);

$openStatesData = new OpenStatesDataProvider(
    dirname(__DIR__) . '/resources/data/' . 'SC_2023-2024_bills.json'
);

$report = new BadBillSponsors($worksheets, $openStatesData);

echo $report;
