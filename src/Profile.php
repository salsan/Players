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
    private DOMXPath $xpath;
    private string $url = "https://www.torneionline.com/giocatori_d.php?progre=";

    public function __construct(string $id)
    {
        $this->dom = new DOMDocument();
        $this->url .= $this->getIdFromUrl($id) . "&tipo=a";

        $this->dom = $this->getHTML($this->url, null);
        $this->xpath = new DOMXPath($this->dom);
    }

    public function getProfile(): array
    {
        $profile = [];

        $xpath_profile = "//table[preceding-sibling::table//center[contains(text(), 'Dati di base')]][1]//table//tr[3]";

        $profile = [
            "photo" => $this->getNodeText("//td/a/img[@alt='Modifica o cancella la foto di questo giocatore']/@src"),
            "tranche" => (bool) $this->getNodeText($xpath_profile . "/td[5]//@alt"),
            "norm" => (bool) $this->getNodeText($xpath_profile . "/td[4]//@alt"),
            "name" => $this->getNodeText($xpath_profile . "/td[7]//a"),
            "category" => $this->getNodeText($xpath_profile . "/td[8]/span"),
            "nationalElo" => $this->getNodeText($xpath_profile . "/td[9]/span"),
            "eloFide" => $this->getNodeText($xpath_profile . "/td[10]/span"),
            "eloOnlineBullet" => $this->getNodeText($xpath_profile . "/td[11]/span"),
            "eloOnlineBlitz" => $this->getNodeText($xpath_profile . "/td[12]/span"),
            "eloOnlineRapid" => $this->getNodeText($xpath_profile . "/td[13]/span"),
            "fsiId" => $this->getNodeText($xpath_profile . "/td[14]/span"),
            "fideId" => $this->getNodeText($xpath_profile . "/td[15]/span"),
            "lastTournament" => $this->getNodeText($xpath_profile . "/td[16]/span"),
            "yearBirthday" => $this->getNodeText($xpath_profile . "/td[17]/span"),
            "province" => $this->getNodeText($xpath_profile . "/td[18]/span"),
            "region" => $this->getNodeText($xpath_profile . "/td[19]/span"),
            "gender" => $this->getNodeText($xpath_profile . "/td[20]/span"),
            "stats" => $this->getStatsElo(),
        ];

        return $profile;
    }

    public function getStatsElo(): array
    {
        $stats = [];

        $xpath_stats = "//table[preceding-sibling::table" .
                    "//center[contains(text(), 'Dati di base')]][1]" .
                    "//table[2]//tr[2]/td[3]/span";

        $getValue = $this->replaceWithStandardSpace(
            $this->getNodeText($xpath_stats)
        );

        preg_match_all('/(\d+)\s+\((\w+\s\d+)\)/', $getValue, $matches);

        $stats =  [
            'best' => [
                'elo' => $matches[1][0] ?? 0,
                'date' => $matches[2][0] ?? ''
            ],
            'worst' => [
                'elo' => $matches[1][1] ?? 0,
                'date' => $matches[2][1] ?? ''
            ]
        ];

        return $stats;
    }

    public function getTournaments(): array
    {
        $tournaments = [];

        $row = 1;
        $totalTournaments = $this->getNumberTournaments();

        for ($i = 0; $totalTournaments > $i; $i++) {
            $row = $row + 2;
            $xpath_tournaments = '//center[2]/table//table[6]//tr[' . $row . ']';

            $id = $this->getNodeText($xpath_tournaments . "/td[1]/span");

            $tournaments[$id] = [
                "name" => $this->getNodeText($xpath_tournaments . "/td[2]/span"),
                "province" => $this->getNodeText($xpath_tournaments . "/td[3]/span"),
                "startData" => $this->getNodeText($xpath_tournaments . "/td[4]/span"),
                "endData" => $this->getNodeText($xpath_tournaments . "/td[5]/span"),
                "eloVariation" => $this->getNodeText($xpath_tournaments . "/td[6]/span"),
                "averageOpponent" => $this->getNodeText($xpath_tournaments . "/td[7]/span"),
                "numberOfMatchFide" => $this->getNodeText($xpath_tournaments . "/td[8]/span"),
                "fidePoint" => $this->getNodeText($xpath_tournaments . "/td[9]/span"),
                "performance" => $this->getNodeText($xpath_tournaments . "/td[10]/span"),
                "totalNumberOfRounds" => $this->getNodeText($xpath_tournaments . "/td[11]/span"),
                "totalPlayers" => $this->getNodeText($xpath_tournaments . "/td[12]/span"),
                "rankingPosition" => $this->getNodeText($xpath_tournaments . "/td[13]/span"),
                "points" => $this->getNodeText($xpath_tournaments . "/td[14]/span"),
                "games" => $this->getNodeText($xpath_tournaments . "/td[15]/span"),
                "gamesWin" => $this->getNodeText($xpath_tournaments . "/td[16]/span"),
                "gamesDraw" => $this->getNodeText($xpath_tournaments . "/td[17]/span"),
                "gamesLoss" => $this->getNodeText($xpath_tournaments . "/td[18]/span"),
                "gamesWhite" => $this->getNodeText($xpath_tournaments . "/td[19]/span"),
                "gamesWinWhite" => $this->getNodeText($xpath_tournaments . "/td[20]/span"),
                "gamesDrawWhite" => $this->getNodeText($xpath_tournaments . "/td[21]/span"),
                "gamesLossWhite" => $this->getNodeText($xpath_tournaments . "/td[22]/span"),
                "gamesBlack" => $this->getNodeText($xpath_tournaments . "/td[23]/span"),
                "gamesWinBlack" => $this->getNodeText($xpath_tournaments . "/td[24]/span"),
                "gamesDrawBlack" => $this->getNodeText($xpath_tournaments . "/td[25]/span"),
                "gamesLossBlack" => $this->getNodeText($xpath_tournaments . "/td[26]/span"),
                "gamesWinsForfeit" => $this->getNodeText($xpath_tournaments . "/td[27]/span"),
                "gamesLossForfeit" => $this->getNodeText($xpath_tournaments . "/td[28]/span"),
            ];
        }
        return $tournaments;
    }

    public function getNumberTournaments(): int
    {

        $xpath_tournament = "//center[contains(text(), 'Tornei disputati')]";

        $getValue = $this->replaceWithStandardSpace(
            $this->getNodeText($xpath_tournament)
        );

        preg_match('/\d+/', $getValue, $matches);

        $total = (int) $matches[0];

        return $total;
    }

    public function getMonths(): int
    {
        $xpath_months = "//b[contains(text(), 'Storia Elo')]" .
                        "//following::table[1]" .
                        "//tr[not(td/a[contains(text(), 'Anno')])]";

        $months = $this->xpath->query($xpath_months);

        $months_count = $months->length - 1 ;

        return (int) $months_count;
    }

    public function getElo(): array
    {
        $xpath_elo = "//b[contains(text(), 'Storia Elo')]//following::table[1]";

        $months_count = $this->getMonths();

        $row = 1;
        $elo = [];

        for ($i = 0; $months_count > $i; $i++) {
            $row = $row + 1;

            $xpath_row = $xpath_elo . "//tr[" . $row . "]";

            $year = $this->getNodeText($xpath_row .  "/td[1]/span");
            $month = $this->getNodeText($xpath_row . "/td[2]/span");

            $elo[$year][$month] = [
                "eloNational" => $this->getNodeText($xpath_row . "/td[3]/span"),
                "eloNationalChange" => $this->getNodeText($xpath_row . "/td[4]/span"),
                "eloFide" => $this->getNodeText($xpath_row . "/td[5]/span"),
                "eloFideChange" => $this->getNodeText($xpath_row . "/td[6]/span"),
            ];
        }

        return $elo;
    }

    public function getTournamentsTranches(): array
    {
        $tranche = [];

        $row = 0;

        $number_tranches = $this->getNumbersTranches();

        for ($i = 0; $number_tranches > $i; $i++) {
            $row = $row + 1;

            $xpath_tranches = "//center[contains(text(), 'Tranche conseguite')]" .
            "//following::table[1]//tr[td[10] and td[@bgcolor]][" . $row . "]";

            $id = $this->getNodeText($xpath_tranches . "/td[1]/span");

            $tranche[$id] = [
                "name" =>  mb_convert_encoding($this->getNodeText($xpath_tranches . "/td[2]/span"), 'ISO-8859-1', 'UTF-8'),
                "province" => $this->getNodeText($xpath_tranches . "/td[3]/span"),
                "startData" => $this->getNodeText($xpath_tranches . "/td[4]/span"),
                "endData" => $this->getNodeText($xpath_tranches . "/td[5]/span"),
                "eloVariation" => $this->getNodeText($xpath_tranches . "/td[6]/span"),
                "averageOpponent" => $this->getNodeText($xpath_tranches . "/td[7]/span"),
                "numberOfMatchFide" => $this->getNodeText($xpath_tranches . "/td[8]/span"),
                "fidePoint" => $this->getNodeText($xpath_tranches . "/td[9]/span"),
                "trancheValue" => $this->getNodeText($xpath_tranches . "/td[10]/span"),
            ];
        }

        return $tranche;
    }

    public function getTournamentsNorms(): array
    {
        $norms = [];

        $row = 0;

        $number_norms = $this->getNumbersNorms();

        for ($i = 0; $number_norms > $i; $i++) {
            $row = $row + 1;

            $xpath_norms = "//center[contains(text(), 'Norme di Maestro conseguite')]" .
            "//following::table[1]//tr[td[@bgcolor]][" . $row . "]";

            $id = $this->getNodeText($xpath_norms . "/td[1]/span");

            $norms[$id] = [
                "name" =>  mb_convert_encoding($this->getNodeText($xpath_norms . "/td[2]/span"), 'ISO-8859-1', 'UTF-8'),
                "province" => $this->getNodeText($xpath_norms . "/td[3]/span"),
                "startData" => $this->getNodeText($xpath_norms . "/td[4]/span"),
                "endData" => $this->getNodeText($xpath_norms . "/td[5]/span"),
                "eloVariation" => $this->getNodeText($xpath_norms . "/td[6]/span"),
                "averageOpponent" => $this->getNodeText($xpath_norms . "/td[7]/span"),
                "numberOfMatch" => $this->getNodeText($xpath_norms . "/td[8]/span"),
                "numberOfMatchPlayed" => $this->getNodeText($xpath_norms . "/td[9]/span"),
                "points" => $this->getNodeText($xpath_norms . "/td[10]/span"),
                "pointsRequired" => $this->getNodeText($xpath_norms . "/td[11]/span"),
            ];
        }

        return $norms;
    }

    public function getNumbersNorms(): int
    {

        $xpath_norms = "//center[contains(text(), 'Norme di Maestro conseguite')]" .
                "//following::table[1]//tr[td[@bgcolor]]";

        $norms = $this->xpath->query($xpath_norms);

        $months_count = $norms->length;

        return (int) $months_count;
    }

    public function getNumbersTranches(): int
    {
        $xpath_tranches = "//center[contains(text(), 'Tranche conseguite')]" .
                        "//following::table[1]//tr[td[10] and td[@bgcolor]]";

        $tranches = $this->xpath->query($xpath_tranches);

        $tranches_count = $tranches->length;

        return (int) $tranches_count;
    }

    public function getIdFromUrl($id): string
    {
        $dom = new DOMDocument();
        $url = 'https://www.torneionline.com/giocatori.php?tipo=11&ifsi=' . $id;

        $dom = $this->getHTML($url, null);

        $xpath = new DOMXPath($dom);

        $getURL = $this->getNodeValue(
            $xpath,
            '//table//td[8]/span/a/@href'
        );

        $pattern = '/\d+/';
        preg_match($pattern, $getURL, $profileId);

        return  $profileId[0];
    }

    public function getName(): string
    {
        $xpath_name = "//span[@class='tpolcorpobigbig']/b/text()";

        $name = $this->getNodeText($xpath_name);

        return $name;
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
