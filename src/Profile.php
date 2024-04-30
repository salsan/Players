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

    public function getTournaments(): array
    {
        $tournaments = [];
        $xpath = new DOMXPath($this->dom);
        $xpath_tournaments = "//center[2]/table//table[6]//tr[3]";

        $row = 1;
        $totalTournaments = $this->getNumberTournaments();

        for ($i = 0; $totalTournaments > $i; $i++) {
            $row = $row + 2;
            $xpath_tournaments = '//center[2]/table//table[6]//tr[' . $row . ']';

            $id = $this->getNodeValue(
                $xpath,
                $xpath_tournaments . "/td[1]/span"
            );

            $tournaments[$id]["name"] = $this->getNodeValue(
                $xpath,
                $xpath_tournaments . "/td[2]/span"
            );

            $tournaments[$id]["province"] = $this->getNodeValue(
                $xpath,
                $xpath_tournaments . "/td[3]/span"
            );

            $tournaments[$id]["startData"] = $this->getNodeValue(
                $xpath,
                $xpath_tournaments . "/td[4]/span"
            );

            $tournaments[$id]["endData"] = $this->getNodeValue(
                $xpath,
                $xpath_tournaments . "/td[5]/span"
            );

            $tournaments[$id]["eloVariation"] = $this->getNodeValue(
                $xpath,
                $xpath_tournaments . "/td[6]/span"
            );

            $tournaments[$id]["averageOpponent"] = $this->getNodeValue(
                $xpath,
                $xpath_tournaments . "/td[7]/span"
            );

            $tournaments[$id]["numberOfMatchFide"] = $this->getNodeValue(
                $xpath,
                $xpath_tournaments . "/td[8]/span"
            );

            $tournaments[$id]["fidePoint"] = $this->getNodeValue(
                $xpath,
                $xpath_tournaments . "/td[9]/span"
            );

            $tournaments[$id]["performance"] = $this->getNodeValue(
                $xpath,
                $xpath_tournaments . "/td[10]/span"
            );

            $tournaments[$id]["totalNumberOfRounds"] = $this->getNodeValue(
                $xpath,
                $xpath_tournaments . "/td[11]/span"
            );

            $tournaments[$id]["totalPlayers"] = $this->getNodeValue(
                $xpath,
                $xpath_tournaments . "/td[12]/span"
            );

            $tournaments[$id]["rankingPosition"] = $this->getNodeValue(
                $xpath,
                $xpath_tournaments . "/td[13]/span"
            );

            $tournaments[$id]["points"] = $this->getNodeValue(
                $xpath,
                $xpath_tournaments . "/td[14]/span"
            );

            $tournaments[$id]["games"] = $this->getNodeValue(
                $xpath,
                $xpath_tournaments . "/td[15]/span"
            );

            $tournaments[$id]["gamesWin"] = $this->getNodeValue(
                $xpath,
                $xpath_tournaments . "/td[16]/span"
            );

            $tournaments[$id]["gamesDraw"] = $this->getNodeValue(
                $xpath,
                $xpath_tournaments . "/td[17]/span"
            );

            $tournaments[$id]["gamesLoss"] = $this->getNodeValue(
                $xpath,
                $xpath_tournaments . "/td[18]/span"
            );

            $tournaments[$id]["gamesWhite"] = $this->getNodeValue(
                $xpath,
                $xpath_tournaments . "/td[19]/span"
            );

            $tournaments[$id]["gamesWinWhite"] = $this->getNodeValue(
                $xpath,
                $xpath_tournaments . "/td[20]/span"
            );

            $tournaments[$id]["gamesDrawWhite"] = $this->getNodeValue(
                $xpath,
                $xpath_tournaments . "/td[21]/span"
            );

            $tournaments[$id]["gamesLossWhite"] = $this->getNodeValue(
                $xpath,
                $xpath_tournaments . "/td[22]/span"
            );

            $tournaments[$id]["gamesBlack"] = $this->getNodeValue(
                $xpath,
                $xpath_tournaments . "/td[23]/span"
            );
            $tournaments[$id]["gamesWinBlack"] = $this->getNodeValue(
                $xpath,
                $xpath_tournaments . "/td[24]/span"
            );

            $tournaments[$id]["gamesDrawBlack"] = $this->getNodeValue(
                $xpath,
                $xpath_tournaments . "/td[25]/span"
            );

            $tournaments[$id]["gamesLossBlack"] = $this->getNodeValue(
                $xpath,
                $xpath_tournaments . "/td[26]/span"
            );

            $tournaments[$id]["gamesWinsForfeit"] = $this->getNodeValue(
                $xpath,
                $xpath_tournaments . "/td[27]/span"
            );

            $tournaments[$id]["gamesLossForfeit"] = $this->getNodeValue(
                $xpath,
                $xpath_tournaments . "/td[28]/span"
            );
        }
        return $tournaments;
    }

    public function getNumberTournaments(): int
    {
        $xpath = new DOMXPath($this->dom);
        $xpath_tournament = "//center[contains(text(), 'Tornei disputati')]";

        $getValue = $this->replaceWithStandardSpace(
            $this->getNodeValue($xpath, $xpath_tournament)
        );

        preg_match('/\d+/', $getValue, $matches);

        $total = (int) $matches[0];

        return $total;
    }

    public function getMonths(): int
    {
        $xpath = new DOMXPath($this->dom);

        $xpath_months = "//b[contains(text(), 'Storia Elo')]" .
                        "//following::table[1]" .
                        "//tr[not(td/a[contains(text(), 'Anno')])]";

        $months = $xpath->query($xpath_months);

        $months_count = $months->length - 1 ;

        return (int) $months_count;
    }

    public function getElo(): array
    {
        $xpath = new DOMXPath($this->dom);
        $xpath_elo = "//b[contains(text(), 'Storia Elo')]//following::table[1]";

        $months_count = $this->getMonths();

        $row = 1;
        $elo = [];

        for ($i = 0; $months_count > $i; $i++) {
            $row = $row + 1;

            $xpath_row = $xpath_elo . "//tr[" . $row . "]";

            $year = $this->getNodeValue(
                $xpath,
                $xpath_row .  "/td[1]/span"
            );

            $months = $this->getNodeValue(
                $xpath,
                $xpath_row . "/td[2]/span"
            );

            $elo[$year][$months]["eloNational"] = $this->getNodeValue(
                $xpath,
                $xpath_row . "/td[3]/span"
            );

            $elo[$year][$months]["eloNationalChange"] = $this->getNodeValue(
                $xpath,
                $xpath_row . "/td[4]/span"
            );

            $elo[$year][$months]["eloFide"] = $this->getNodeValue(
                $xpath,
                $xpath_row . "/td[5]/span"
            );

            $elo[$year][$months]["eloFideChange"] = $this->getNodeValue(
                $xpath,
                $xpath_row . "/td[6]/span"
            );
        }

            return $elo;
    }
}
