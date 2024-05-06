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
    private DOMXPath $xpath;
    private string $url = "https://www.torneionline.com/giocatori.php";

    public function __construct(array $params)
    {
        $this->dom = new DOMDocument();

        $query = http_build_query($params);

        $this->url .= '?' . $query .  '&tipo=1';
        $this->dom = $this->getHTML($this->url, null);

        $this->xpath = new DOMXPath($this->dom);
    }

    public function getNumber(): int
    {
        $total = $this->getNodeText('//span[@class="tpolcorpobigbig"]/b');

        return (int) $total;
    }

    public function getList(): iterable
    {
        $players_total = $this->getNumber();

        $players = [];
        $row = 2;

        for ($i = 0; $players_total > $i; $i++) {
            $row = $row + 1;

            $xpath_player = '//center[2]/table//table/tr[' . $row . ']';

            $id = $this->getID($xpath_player);

            $players[$id] = [
                'norm' => (bool) $this->getNodeText($xpath_player . '/td[5]//@alt'),
                'tranche' => (bool) $this->getNodeText($xpath_player . '/td[6]//@alt'),
                'name' => $this->getNodeText($xpath_player . '/td[8]//a'),
                'category' => $this->getNodeText($xpath_player . '/td[9]/span'),
                'eloNational' => $this->getNodeText($xpath_player . '/td[10]'),
                'eloFIDE' => $this->getNodeText($xpath_player . '/td[11]'),
                'eloOnlineBullet' => $this->getNodeText($xpath_player . '/td[12]'),
                'eloOnlineBlitz' => $this->getNodeText($xpath_player . '/td[13]'),
                'eloOnlineStandard' => $this->getNodeText($xpath_player . '/td[14]'),
                'nationalID' => $this->getNodeText($xpath_player . '/td[15]'),
                'fideID' => $this->getNodeText($xpath_player . '/td[16]'),
                'lastTournament' => $this->getNodeText($xpath_player . '/td[17]'),
                'birthdayYear' => $this->getNodeText($xpath_player . '/td[18]'),
                'province' => $this->getNodeText($xpath_player . '/td[19]'),
                'region' => $this->getNodeText($xpath_player . '/td[20]'),
                'gender' => $this->getNodeText($xpath_player . '/td[21]'),
            ];
        }

        return $players;
    }

    private function getID($xpath_root): string
    {

        $id = $this->getNodeText($xpath_root . '/td[15]');

        return $id;
    }

    private function getNodeText($node): string
    {
        $data = $this->getNodeValue(
            $this->xpath,
            $node
        );

        return $data;
    }
}
