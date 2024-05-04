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

        $profile["photo"] = $this->getNodeValue(
            $this->xpath,
            "//td/a/img[@alt='Modifica o cancella la foto di questo giocatore']/@src"
        );

        $profile["tranche"] = (bool) $this->getNodeValue(
            $this->xpath,
            $xpath_profile . "/td[5]//@alt"
        );

        $profile["norm"] = (bool) $this->getNodeValue(
            $this->xpath,
            $xpath_profile . "/td[4]//@alt"
        );

        $profile["name"] = $this->getNodeValue(
            $this->xpath,
            $xpath_profile . "/td[7]//a"
        );

        $profile["category"] = $this->getNodeValue(
            $this->xpath,
            $xpath_profile . "/td[8]/span"
        );

        $profile["nationalElo"] = $this->getNodeValue(
            $this->xpath,
            $xpath_profile . "/td[9]/span"
        );

        $profile["eloFide"] = $this->getNodeValue(
            $this->xpath,
            $xpath_profile . "/td[10]/span"
        );

        $profile["eloOnlineBullet"] = $this->getNodeValue(
            $this->xpath,
            $xpath_profile . "/td[11]/span"
        );

        $profile["eloOnlineBlitz"] = $this->getNodeValue(
            $this->xpath,
            $xpath_profile . "/td[12]/span"
        );

        $profile["eloOnlineRapid"] = $this->getNodeValue(
            $this->xpath,
            $xpath_profile . "/td[13]/span"
        );

        $profile["fsiId"] = $this->getNodeValue(
            $this->xpath,
            $xpath_profile . "/td[14]/span"
        );

        $profile["fideId"] = $this->getNodeValue(
            $this->xpath,
            $xpath_profile . "/td[15]/span"
        );

        $profile["lastTournament"] = $this->getNodeValue(
            $this->xpath,
            $xpath_profile . "/td[16]/span"
        );

        $profile["yearBirthday"] = $this->getNodeValue(
            $this->xpath,
            $xpath_profile . "/td[17]/span"
        );

        $profile["province"] = $this->getNodeValue(
            $this->xpath,
            $xpath_profile . "/td[18]/span"
        );

        $profile["region"] = $this->getNodeValue(
            $this->xpath,
            $xpath_profile . "/td[19]/span"
        );

        $profile["gender"] = $this->getNodeValue(
            $this->xpath,
            $xpath_profile . "/td[20]/span"
        );

        $profile["stats"] = $this->getStatsElo();

        return $profile;
    }

    public function getStatsElo(): array
    {
        $stats = [];

        $xpath_stats = "//table[preceding-sibling::table" .
                    "//center[contains(text(), 'Dati di base')]][1]" .
                    "//table[2]//tr[2]/td[3]/span";

        $getValue = $this->replaceWithStandardSpace(
            $this->getNodeValue($this->xpath, $xpath_stats)
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

        $xpath_tournaments = "//center[2]/table//table[6]//tr[3]";

        $row = 1;
        $totalTournaments = $this->getNumberTournaments();

        for ($i = 0; $totalTournaments > $i; $i++) {
            $row = $row + 2;
            $xpath_tournaments = '//center[2]/table//table[6]//tr[' . $row . ']';

            $id = $this->getNodeValue(
                $this->xpath,
                $xpath_tournaments . "/td[1]/span"
            );

            $tournaments[$id]["name"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tournaments . "/td[2]/span"
            );

            $tournaments[$id]["province"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tournaments . "/td[3]/span"
            );

            $tournaments[$id]["startData"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tournaments . "/td[4]/span"
            );

            $tournaments[$id]["endData"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tournaments . "/td[5]/span"
            );

            $tournaments[$id]["eloVariation"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tournaments . "/td[6]/span"
            );

            $tournaments[$id]["averageOpponent"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tournaments . "/td[7]/span"
            );

            $tournaments[$id]["numberOfMatchFide"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tournaments . "/td[8]/span"
            );

            $tournaments[$id]["fidePoint"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tournaments . "/td[9]/span"
            );

            $tournaments[$id]["performance"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tournaments . "/td[10]/span"
            );

            $tournaments[$id]["totalNumberOfRounds"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tournaments . "/td[11]/span"
            );

            $tournaments[$id]["totalPlayers"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tournaments . "/td[12]/span"
            );

            $tournaments[$id]["rankingPosition"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tournaments . "/td[13]/span"
            );

            $tournaments[$id]["points"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tournaments . "/td[14]/span"
            );

            $tournaments[$id]["games"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tournaments . "/td[15]/span"
            );

            $tournaments[$id]["gamesWin"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tournaments . "/td[16]/span"
            );

            $tournaments[$id]["gamesDraw"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tournaments . "/td[17]/span"
            );

            $tournaments[$id]["gamesLoss"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tournaments . "/td[18]/span"
            );

            $tournaments[$id]["gamesWhite"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tournaments . "/td[19]/span"
            );

            $tournaments[$id]["gamesWinWhite"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tournaments . "/td[20]/span"
            );

            $tournaments[$id]["gamesDrawWhite"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tournaments . "/td[21]/span"
            );

            $tournaments[$id]["gamesLossWhite"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tournaments . "/td[22]/span"
            );

            $tournaments[$id]["gamesBlack"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tournaments . "/td[23]/span"
            );
            $tournaments[$id]["gamesWinBlack"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tournaments . "/td[24]/span"
            );

            $tournaments[$id]["gamesDrawBlack"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tournaments . "/td[25]/span"
            );

            $tournaments[$id]["gamesLossBlack"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tournaments . "/td[26]/span"
            );

            $tournaments[$id]["gamesWinsForfeit"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tournaments . "/td[27]/span"
            );

            $tournaments[$id]["gamesLossForfeit"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tournaments . "/td[28]/span"
            );
        }
        return $tournaments;
    }

    public function getNumberTournaments(): int
    {

        $xpath_tournament = "//center[contains(text(), 'Tornei disputati')]";

        $getValue = $this->replaceWithStandardSpace(
            $this->getNodeValue($this->xpath, $xpath_tournament)
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

            $year = $this->getNodeValue(
                $this->xpath,
                $xpath_row .  "/td[1]/span"
            );

            $months = $this->getNodeValue(
                $this->xpath,
                $xpath_row . "/td[2]/span"
            );

            $elo[$year][$months]["eloNational"] = $this->getNodeValue(
                $this->xpath,
                $xpath_row . "/td[3]/span"
            );

            $elo[$year][$months]["eloNationalChange"] = $this->getNodeValue(
                $this->xpath,
                $xpath_row . "/td[4]/span"
            );

            $elo[$year][$months]["eloFide"] = $this->getNodeValue(
                $this->xpath,
                $xpath_row . "/td[5]/span"
            );

            $elo[$year][$months]["eloFideChange"] = $this->getNodeValue(
                $this->xpath,
                $xpath_row . "/td[6]/span"
            );
        }

            return $elo;
    }

    public function getTournamentsTranches(): array
    {
        $tranche = [];


        $xpath_tranches = "//center[contains(text(), 'Tranche conseguite')]" .
        "//following::table[1]//tr[td[10] and td[@bgcolor]]";

        $row = 0;

        $number_tranches = $this->getNumbersTranches();

        for ($i = 0; $number_tranches > $i; $i++) {
            $row = $row + 1;

            $xpath_tranches = "//center[contains(text(), 'Tranche conseguite')]" .
            "//following::table[1]//tr[td[10] and td[@bgcolor]][" . $row . "]";

            $id = $this->getNodeValue(
                $this->xpath,
                $xpath_tranches . "/td[1]/span"
            );

            $tranche[$id]["name"] = mb_convert_encoding(
                $this->getNodeValue(
                    $this->xpath,
                    $xpath_tranches . "/td[2]/span"
                ),
                'ISO-8859-1',
                'UTF-8'
            );

            $tranche[$id]["province"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tranches . "/td[3]/span"
            );

            $tranche[$id]["startData"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tranches . "/td[4]/span"
            );

            $tranche[$id]["endData"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tranches . "/td[5]/span"
            );

            $tranche[$id]["eloVariation"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tranches . "/td[6]/span"
            );

            $tranche[$id]["averageOpponent"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tranches . "/td[7]/span"
            );

            $tranche[$id]["numberOfMatchFide"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tranches . "/td[8]/span"
            );

            $tranche[$id]["fidePoint"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tranches . "/td[9]/span"
            );

            $tranche[$id]["trancheValue"] = $this->getNodeValue(
                $this->xpath,
                $xpath_tranches . "/td[10]/span"
            );
        }

        return $tranche;
    }

    public function getTournamentsNorms(): array
    {
        $norms = [];


        $xpath_norms = "//center[contains(text(), 'Norme di Maestro conseguite')]" .
        "//following::table[1]//tr[td[@bgcolor]]";

        $row = 0;

        $number_norms = $this->getNumbersNorms();

        for ($i = 0; $number_norms > $i; $i++) {
            $row = $row + 1;

            $xpath_norms = "//center[contains(text(), 'Norme di Maestro conseguite')]" .
            "//following::table[1]//tr[td[@bgcolor]][" . $row . "]";

            $id = $this->getNodeValue(
                $this->xpath,
                $xpath_norms . "/td[1]/span"
            );

            $norms[$id]["name"] = mb_convert_encoding(
                $this->getNodeValue(
                    $this->xpath,
                    $xpath_norms . "/td[2]/span"
                ),
                'ISO-8859-1',
                'UTF-8'
            );

            $norms[$id]["province"] = $this->getNodeValue(
                $this->xpath,
                $xpath_norms . "/td[3]/span"
            );

            $norms[$id]["startData"] = $this->getNodeValue(
                $this->xpath,
                $xpath_norms . "/td[4]/span"
            );

            $norms[$id]["endData"] = $this->getNodeValue(
                $this->xpath,
                $xpath_norms . "/td[5]/span"
            );

            $norms[$id]["eloVariation"] = $this->getNodeValue(
                $this->xpath,
                $xpath_norms . "/td[6]/span"
            );

            $norms[$id]["averageOpponent"] = $this->getNodeValue(
                $this->xpath,
                $xpath_norms . "/td[7]/span"
            );

            $norms[$id]["numberOfMatch"] = $this->getNodeValue(
                $this->xpath,
                $xpath_norms . "/td[8]/span"
            );

            $norms[$id]["numberOfMatchPlayed"] = $this->getNodeValue(
                $this->xpath,
                $xpath_norms . "/td[9]/span"
            );

            $norms[$id]["Points"] = $this->getNodeValue(
                $this->xpath,
                $xpath_norms . "/td[10]/span"
            );

            $norms[$id]["PointsRequired"] = $this->getNodeValue(
                $this->xpath,
                $xpath_norms . "/td[11]/span"
            );
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



        $name = $this->getNodeValue(
            $this->xpath,
            $xpath_name
        );

        return $name;
    }
}
