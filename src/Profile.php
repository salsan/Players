<?php

declare(strict_types=1);

namespace Salsan\Players;

use Salsan\Utils\String\HiddenSpaceTrait;
use Salsan\Utils\DOM\DOMDocumentTrait;
use DOMDocument;
use DOMXPath;

class Profile
{
    use HiddenSpaceTrait;
    use DOMDocumentTrait;

    private DOMDocument $dom;
    private string $url = "https://www.torneionline.com/giocatori_d.php?progre=";

    public function __construct(string $id)
    {
        $this->dom = new DOMDocument();
        $this->url .= $id . "&tipo=a";

        $this->dom = $this->getHTML($this->url, null);
    }

    public function getProfile(): array
    {
        $profile = [];
        $xpath = new DOMXPath($this->dom);

        $xpath_profile = "//table[preceding-sibling::table//center[contains(text(), 'Dati di base')]][1]//table//tr[3]";

        $profile["tranche"] = (bool) $this->getNodeValue(
            $xpath,
            $xpath_profile . "/td[5]//@alt"
        );


        $profile["norm"] = (bool) $this->getNodeValue(
            $xpath,
            $xpath_profile . "/td[4]//@alt"
        );


        $profile["name"] = $this->getNodeValue(
            $xpath,
            $xpath_profile . "/td[7]//a"
        );


        $profile["category"] = $this->getNodeValue(
            $xpath,
            $xpath_profile . "/td[8]/span"
        );


        $profile["nationalElo"] = $this->getNodeValue(
            $xpath,
            $xpath_profile . "/td[9]/span"
        );

        $profile["eloFide"] = $this->getNodeValue(
            $xpath,
            $xpath_profile . "/td[10]/span"
        );


        $profile["eloOnlineBullet"] = $this->getNodeValue(
            $xpath,
            $xpath_profile . "/td[11]/span"
        );

        $profile["eloOnlineBlitz"] = $this->getNodeValue(
            $xpath,
            $xpath_profile . "/td[12]/span"
        );

        $profile["eloOnlineRapid"] = $this->getNodeValue(
            $xpath,
            $xpath_profile . "/td[13]/span"
        );

        $profile["fsiId"] = $this->getNodeValue(
            $xpath,
            $xpath_profile . "/td[14]/span"
        );

        $profile["fideId"] = $this->getNodeValue(
            $xpath,
            $xpath_profile . "/td[15]/span"
        );

        $profile["lastTournament"] = $this->getNodeValue(
            $xpath,
            $xpath_profile . "/td[16]/span"
        );

        $profile["yearBirthday"] = $this->getNodeValue(
            $xpath,
            $xpath_profile . "/td[17]/span"
        );

        $profile["province"] = $this->getNodeValue(
            $xpath,
            $xpath_profile . "/td[18]/span"
        );

        $profile["region"] = $this->getNodeValue(
            $xpath,
            $xpath_profile . "/td[19]/span"
        );

        $profile["gender"] = $this->getNodeValue(
            $xpath,
            $xpath_profile . "/td[20]/span"
        );

        $profile["stats"] = $this->getStatsElo();

        return $profile;
    }

    public function getStatsElo(): array
    {
        $stats = [];
        $xpath = new DOMXPath($this->dom);
        $xpath_stats = "//table[preceding-sibling::table" .
            "//center[contains(text(), 'Dati di base')]][1]" .
            "//table[2]//tr[2]/td[3]/span";

        $getValue = $this->replaceWithStandardSpace(
            $this->getNodeValue($xpath, $xpath_stats)
        );

        preg_match_all('/(\d+)\s+\((\w+\s\d+)\)/', $getValue, $matches);

        $stats =  [
            'best' => [
                'elo' => $matches[1][0],
                'date' => $matches[2][0]
            ],
            'worst' => [
                'elo' => $matches[1][1],
                'date' => $matches[2][1]
            ]
        ];

        return $stats;
    }
}
