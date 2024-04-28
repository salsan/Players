<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Salsan\Players;

final class ProfileTest extends TestCase
{
    private $id = '27715';

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
        $this->assertStringContainsString('165714' , $player["fsiId"]);
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
}
