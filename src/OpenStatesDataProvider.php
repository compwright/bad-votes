<?php

namespace Compwright\BadVotes;

class OpenStatesDataProvider
{
    private array $data = [];

    public function __construct(string $jsonFile)
    {
        $data = json_decode(file_get_contents($jsonFile));

        $this->data = array_combine(
            array_column($data, 'identifier'),
            $data
        );
    }

    /**
     * @return string[]
     */
    public function getBillSponsors(string $bill): array
    {
        if (!array_key_exists($bill, $this->data)) {
            return [];
        }

        return array_column(
            $this->data[$bill]->sponsors,
            'name'
        );
    }

    public function getBillPrimarySponsor(string $bill): ?string
    {
        return $this->getLegislatorTitle($bill) . ($this->getBillSponsors($bill)[0] ?? null);
    }

    public function getBillCoSponsors(string $bill): array
    {
        return array_map(
            fn (string $legislator) => $this->getLegislatorTitle($bill) . $legislator,
            array_slice($this->getBillSponsors($bill), 1)
        );
    }

    public function getBillVotes(string $bill): array
    {
        return $this->data[$bill]->votes;
    }

    private function getLegislatorTitle(string $bill): string
    {
        if (!array_key_exists($bill, $this->data)) {
            return '';
        }

        return $this->data[$bill]->chamber === 'lower' ? 'Rep. ' : 'Sen. ';
    }
}
