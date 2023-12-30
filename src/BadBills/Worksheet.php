<?php

namespace Compwright\BadVotes\BadBills;

class Worksheet
{
    public string $bill;

    public ?string $version;

    public string $notes;

    public int $score;

    public ?float $fiscalImpact;

    public array $objections = [];

    public static function newFromCsv(array $columns): self
    {
        $sheet = new self;
        $parts = preg_split("/[\(|\)]/", $columns[0]);
        $sheet->bill = trim($parts[0] ?? '');
        $sheet->version = trim($parts[1] ?? '') ?: null;
        $sheet->notes = $columns[1];
        $sheet->score = (int) $columns[2];
        $sheet->fiscalImpact = $columns[3] ? (float) $columns[4] : null;
        for ($i = 4; $i <= 25; $i += 2) {
            $objection = $columns[$i];
            $explanation = $columns[$i+1];
            $sheet->objections[$objection] = $explanation;
        }
        return $sheet;
    }
}
