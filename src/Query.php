<?php

declare(strict_types=1);

namespace Salsan\Players;

use Salsan\Utils\String\HiddenSpaceTrait;
use Salsan\Utils\DOM\DOMDocumentTrait;
use DOMDocument;
use DOMXPath;

class Query
{
    use HiddenSpaceTrait;
    use DOMDocumentTrait;

    private DOMDocument $dom;
    private string $url = "https://www.torneionline.com/giocatori.php";

    public function __construct(array $params)
    {
        $this->dom = new DOMDocument();

        // Players Parameters
        $name = $params['name'] ?? '';
        $categoryMin = $params['cat1'] ?? '';
        $categoryMax = $params['cat2'] ?? '';
        $eloNationalMin = $params['eli1'] ?? '';
        $eloNationalMax = $params['eli2'] ?? '';
        $eloFIDEMin = $params['elf1'] ?? '';
        $eloFIDEMax = $params['elf2'] ?? '';
        $eloOnlineBullet = $params['are1'] ?? '';
        $eloOnlineBlitz = $params['are2'] ?? '';
        $eloOnlineStandard = $params['are3'] ?? '';
        $birthdayFrom = $params['ann1'] ?? '';
        $birthdayTo = $params['ann2'] ?? '';
        $gender = $params['sess'] ?? '';
        $isTournmentPlayed = $params['ptor'] ?? '';
        $isTrancheFIDE = $params['ptra'] ?? '';
        $isNationalNorm = $params['pmas'] ?? '';
        $lastTournamentPlayedFrom = $params['ult1'] ?? '';
        $lastTournamentPlayedTo = $params['ult2'] ?? '';
        $provinceFirst = $params['pro1'] ?? '';
        $provinceSecond = $params['pro2'] ?? '';
        $provinceThird = $params['pro3'] ?? '';
        $regionFirst = $params['reg1'] ?? '';
        $regionSecond = $params['reg2'] ?? '';
        $regionThird = $params['reg3'] ?? '';
        $nationalID = $params['ifsi'] ?? '';
        $fideID = $params['ifid'] ?? '';
        $orderFirst = $params['ord1'] ?? '';
        $orderSecond = $params['ord2'] ?? '';
        $orderThird = $params['ord3'] ?? '';
        $directionFirst = $params['sen1'] ?? '';
        $directionSecond = $params['sen2'] ?? '';
        $directionThird = $params['sen3'] ?? '';


        $this->url .=
            "?nome={$name}" .
            "&cat1={$categoryMin}" .
            "&cat2={$categoryMax}" .
            "&eli1={$eloNationalMin}" .
            "&eli2={$eloNationalMax}" .
            "&elf1={$eloFIDEMin}" .
            "&elf2={$eloFIDEMax}" .
            "&are1={$eloOnlineBullet}" .
            "&are2={$eloOnlineBlitz}" .
            "&are3={$eloOnlineStandard}" .
            "&ann1={$birthdayFrom}" .
            "&ann2={$birthdayTo}" .
            "&sess={$gender}" .
            "&ptor={$isTournmentPlayed}" .
            "&ptra={$isTrancheFIDE}" .
            "&pmas={$isNationalNorm}" .
            "&ult1={$lastTournamentPlayedFrom}" .
            "&ult2={$lastTournamentPlayedTo}" .
            "&pro1={$provinceFirst}" .
            "&pro2={$provinceSecond}" .
            "&pro3={$provinceThird}" .
            "&reg1={$regionFirst}" .
            "&reg2={$regionSecond}" .
            "&reg3={$regionThird}" .
            "&ifsi={$nationalID}" .
            "&ifid={$fideID}" .
            "&ord1={$orderFirst}" .
            "&sen1={$directionFirst}" .
            "&ord2={$orderSecond}" .
            "&sen2={$directionSecond}" .
            "&ord3={$orderThird}" .
            "&sen3={$directionThird}" .
            "&tipo=1";

        $this->dom = $this->getHTML($this->url, null);
    }

    public function getNumber(): int
    {
        $xpath = new DOMXPath($this->dom);
        $total = $this->getNodeValue(
            $xpath,
            '//span[@class="tpolcorpobigbig"]/b'
        );

        return (int) $total;
    }

    public function getList(): iterable
    {
        $xpath = new DOMXPath($this->dom);
        $players_total = $this->getNumber();

        $players = [];
        $row = 2;

        for ($i = 0; $players_total > $i; $i++) {
            $row = $row + 1;

            $xpath_player = '//center[2]/table//table/tr[' . $row . ']';

            $id = $this->getID($xpath, $xpath_player);

            // National Norm Status
            $players[$id]['norm'] = (bool) $this->getNodeValue(
                $xpath,
                $xpath_player . '/td[5]//@alt'
            );
            // Tranche ( bool )
            $players[$id]['tranche'] = (bool) $this->getNodeValue(
                $xpath,
                $xpath_player . '/td[6]//@alt'
            );

            // Player Name
            $players[$id]['name'] = $this->getNodeValue(
                $xpath,
                $xpath_player . '/td[8]//a'
            );
            // Category
            $players[$id]['category'] = $this->getNodeValue(
                $xpath,
                $xpath_player . '/td[9]/span'
            );

            // ELO Italy
            $players[$id]['eloNational'] = $this->getNodeValue(
                $xpath,
                $xpath_player . '/td[10]'
            );
            // ELO FIDE
            $players[$id]['eloFIDE'] = $this->getNodeValue(
                $xpath,
                $xpath_player . '/td[11]'
            );

            // Arena Online Bullet
            $players[$id]['eloOnlineBullet'] = $this->getNodeValue(
                $xpath,
                $xpath_player . '/td[12]'
            );
            // Arena Online Blitz
            $players[$id]['eloOnlineBlitz'] = $this->getNodeValue(
                $xpath,
                $xpath_player . '/td[13]'
            );
            // Arena Online Standard
            $players[$id]['eloOnlineStandard'] = $this->getNodeValue(
                $xpath,
                $xpath_player . '/td[14]'
            );

            // FSI ID
            $players[$id]['nationalID'] = $this->getNodeValue(
                $xpath,
                $xpath_player . '/td[15]'
            );

            // FIDE ID
            $players[$id]['fideID'] = $this->getNodeValue(
                $xpath,
                $xpath_player . '/td[16]'
            );

            // Last Tournament
            $players[$id]['lastTournament'] = $this->getNodeValue(
                $xpath,
                $xpath_player . '/td[17]'
            );

            // Birthday Year
            $players[$id]['birthdayYear'] = $this->getNodeValue(
                $xpath,
                $xpath_player . '/td[18]'
            );

            // Province
            $players[$id]['province'] = $this->getNodeValue(
                $xpath,
                $xpath_player . '/td[19]'
            );

            // Region
            $players[$id]['region'] = $this->getNodeValue(
                $xpath,
                $xpath_player . '/td[20]'
            );

            // gender
            $players[$id]['gender'] = $this->getNodeValue(
                $xpath,
                $xpath_player . '/td[21]'
            );
        }

        return $players;
    }

    private function getID($xpath, $xpath_root): string
    {
        $id = $this->getNodeValue(
            $xpath,
            $xpath_root . '/td[15]'
        );

        return $id;
    }
}
