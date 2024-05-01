<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Salsan\Players;

final class ProfileTest extends TestCase
{
    private $id = '165714';

    public function testInit(): object
    {
        $profile = new Players\Profile($this->id);
        $this->assertIsObject($profile);
        return $profile;
    }

    /**
     * @depends testInit
     */
    public function testProfile($profile): void
    {
        $player = $profile->getProfile();
        $this->assertNotTrue($player["tranche"]);
        $this->assertNotTrue($player["norm"]);
        $this->assertStringContainsStringIgnoringCase('SANTAGATI', $player["name"]);
        $this->assertStringContainsStringIgnoringCase('2N', $player["category"]);
        $this->assertStringContainsStringIgnoringCase('M', $player["gender"]);
        $this->assertStringContainsString('0', $player["nationalElo"]);
        $this->assertLessThanOrEqual('2000', $player["eloFide"]);
        $this->assertStringContainsString('-', $player["eloOnlineBullet"]);
        $this->assertStringContainsString('-', $player["eloOnlineBlitz"]);
        $this->assertStringContainsString('-', $player["eloOnlineRapid"]);
        $this->assertStringContainsString('165714', $player["fsiId"]);
        $this->assertStringContainsString('2832408', $player["fideId"]);
        $this->assertMatchesRegularExpression('/^\d{2}-\d{2}-\d{4}$/', $player["lastTournament"]);
        $this->assertStringContainsString('1982', $player["yearBirthday"]);
        $this->assertStringContainsString('CT', $player["province"]);
        $this->assertStringContainsString('SIC', $player["region"]);
        $this->assertStringContainsString('M', $player["gender"]);
        $this->assertGreaterThanOrEqual('1656', $player["stats"]["best"]["elo"]);
        $this->assertLessThanOrEqual('1419', $player["stats"]["worst"]["elo"]);
    }

    /**
     * @depends testInit
     */
    public function testGetNumberTournaments($profile): void
    {
        $total = $profile->getNumberTournaments();
        $this->assertLessThanOrEqual(19, $total);
    }

    /**
     * @depends testInit
     */
    public function testGetTournaments($profile): void
    {
        $tournaments = $profile->getTournaments();
        $this->assertIsArray($tournaments);
        $this->assertStringContainsStringIgnoringCase('FESTIVAL MILAZZO ISOLE EOLIE - B', $tournaments["2209005B"]["name"]);
        $this->assertStringContainsString('CT', $tournaments["1501012A"]["province"]);
        $this->assertStringContainsString('02-01-2015', $tournaments["1501012A"]["startData"]);
        $this->assertStringContainsString('04-01-2015', $tournaments["1501012A"]["endData"]);
        $this->assertStringContainsString('0', $tournaments["2209005B"]["eloVariation"]);
        $this->assertStringContainsString('1419' , $tournaments["2209005B"]["averageOpponent"]);
        $this->assertStringContainsString('7' , $tournaments["2209005B"]["numberOfMatchFide"]);
        $this->assertStringContainsString('2.5' , $tournaments["2209005B"]["fidePoint"]);
        $this->assertStringContainsString('1621' , $tournaments["2403032D"]["performance"]);
        $this->assertStringContainsString('5' , $tournaments["2403032D"]["totalNumberOfRounds"]);
        $this->assertStringContainsString('279' , $tournaments["2403032D"]["totalPlayers"]);
        $this->assertStringContainsString('98' , $tournaments["2403032D"]["rankingPosition"]);
        $this->assertStringContainsString('2.5' , $tournaments["2403032D"]["points"]);
        $this->assertStringContainsString('5', $tournaments["2403032D"]["games"]);
        $this->assertStringContainsString('2', $tournaments["2403032D"]["gamesWin"]);
        $this->assertStringContainsString('1', $tournaments["2403032D"]["gamesDraw"]);
        $this->assertStringContainsString('2', $tournaments["2403032D"]["gamesLoss"]);
        $this->assertStringContainsString('2', $tournaments["2403032D"]["gamesWinWhite"]);
        $this->assertStringContainsString('0', $tournaments["2403032D"]["gamesDrawWhite"]);
        $this->assertStringContainsString('0', $tournaments["2403032D"]["gamesLossWhite"]);
        $this->assertStringContainsString('0', $tournaments["2403032D"]["gamesWinBlack"]);
        $this->assertStringContainsString('1', $tournaments["2403032D"]["gamesDrawBlack"]);
        $this->assertStringContainsString('2', $tournaments["2403032D"]["gamesLossBlack"]);
        $this->assertStringContainsString('0', $tournaments["2403032D"]["gamesWinsForfeit"]);
        $this->assertStringContainsString('0', $tournaments["2403032D"]["gamesLossForfeit"]);
        $this->assertStringContainsString('2', $tournaments["2403032D"]["gamesWhite"]);
        $this->assertStringContainsString('3', $tournaments["2403032D"]["gamesBlack"]);
    }

    /**
     * @depends testInit
     */
     public function testgetMonths($profile): void
     {

        $months = $profile->getMonths();
        $this->assertGreaterThanOrEqual('112', $months);

     }

    /**
     * @depends testInit
     */
    public function testgetElo($profile): void
    {
        $elo = $profile->getElo();
        $this->assertStringContainsString('',  $elo["2024"]["4"]["eloNational"]);
        $this->assertStringContainsString('1643', $elo["2024"]["4"]["eloFide"]);
        $this->assertStringContainsString('27', $elo["2018"]["4"]["eloNationalChange"]);
        $this->assertStringContainsString('229', $elo["2024"]["3"]["eloFideChange"]);
    }

    public function testGetNumberNorms(): void
    {
        $profile = new Players\Profile("101314");
        $this->assertIsObject($profile);
        $this->assertGreaterThanOrEqual("3", $profile->getNumbersNorms());
    }
}
