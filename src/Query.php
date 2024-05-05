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

        $query = http_build_query($params);

        $this->url .= '?' . $query .  '&tipo=1';

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
