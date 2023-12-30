<?php

namespace Compwright\BadVotes\BadBills;

use League\Csv\Reader;

class WorksheetCollection
{
    private array $worksheets = [];

    private array $billIndex = [];

    /**
     * @param Worksheet[] $worksheets
     */
    public function __construct(array $worksheets)
    {
        $this->worksheets = $worksheets;
        $this->billIndex = array_column($worksheets, 'bill');
    }

    public static function readFromCsv(string $file): self
    {
        $reader = Reader::createFromPath($file);
        $worksheets = array_map(
            fn (array $columns) => Worksheet::newFromCsv($columns),
            iterator_to_array($reader->getRecords())
        );
        return new self($worksheets);
    }

    public function all(): array
    {
        return $this->worksheets;
    }

    /**
     * @return string[]
     */
    public function getBillList(): array
    {
        return array_unique($this->billIndex);
    }

    /**
     * @return Worksheet[]
     */
    public function getWorksheets(string $bill): array
    {
        return array_filter(
            $this->worksheets,
            fn (Worksheet $w) => $w->bill === $bill
        );
    }

    public function getBillScore(string $bill): float
    {
        return min(
            array_column(
                $this->getWorksheets($bill),
                'score'
            )
        );
    }
}
