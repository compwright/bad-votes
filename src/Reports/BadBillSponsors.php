<?php

namespace Compwright\BadVotes\Reports;

use Compwright\BadVotes\BadBills\WorksheetCollection;
use Compwright\BadVotes\OpenStatesDataProvider;

class BadBillSponsors
{
    private WorksheetCollection $worksheets;

    private OpenStatesDataProvider $billDataProvider;

    public function __construct(WorksheetCollection $worksheets, OpenStatesDataProvider $billDataProvider)
    {
        $this->worksheets = $worksheets;
        $this->billDataProvider = $billDataProvider;
    }

    public function getBillsBySponsor(): array
    {
        $array = [];
        foreach ($this->worksheets->getBillList() as $bill) {
            $sponsor = $this->billDataProvider->getBillPrimarySponsor($bill);
            if (!array_key_exists($sponsor, $array)) {
                $array[$sponsor] = ['count' => 0, 'totalScore' => 0, 'bills' => []];
            }
            $score = $this->worksheets->getBillScore($bill);
            $array[$sponsor]['count']++;
            $array[$sponsor]['totalScore'] += $score;
            $array[$sponsor]['bills'][$bill] = $score;
        }
        return $array;
    }

    public function __toString(): string
    {
        $str = 'Legislator,Number of Bad Bills,Total Bad Bill Score' . PHP_EOL;
        foreach ($this->getBillsBySponsor() as $legislator => $report) {
            $str .= sprintf(
                '%s,%d,%d,%s' . PHP_EOL,
                $legislator,
                $report['count'],
                $report['totalScore'],
                implode(', ', array_map(
                    fn ($score, $bill) => sprintf('%s (%d)', $bill, $score),
                    array_values($report['bills']),
                    array_keys($report['bills'])
                ))
            );
        }
        return $str;
    }
}
