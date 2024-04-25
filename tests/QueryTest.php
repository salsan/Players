<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Salsan\Players;

final class QueryTest extends TestCase
{
    private $paramters = array(
        'name' => 'Santagati',
    );

    public function testInit(): object
    {
        $players = new Players\Query($this->paramters);
        $this->assertIsObject($players);

        return $players;
    }

    /**
     * @depends testInit
     */
    public function testGetNumber($players): void
    {
        $playersNumber = $players->getNumber();
        $this->assertIsInt($playersNumber);
        $this->assertLessThanOrEqual(5, $playersNumber);
    }


    /**
     * @depends testInit
     */
    public function testGetInfo($players): void
    {
        $player = $players->getList();
        $this->assertStringContainsStringIgnoringCase("SANTAGATI SALVATORE", $player["27715"]["name"]);
        $this->assertStringContainsStringIgnoringCase("FM", $player["27711"]["category"]);
        $this->assertStringContainsStringIgnoringCase("0", $player["27711"]["eloNational"]);
        $this->assertStringContainsStringIgnoringCase("1976", $player["27713"]["birthdayYear"]);
        $this->assertStringContainsStringIgnoringCase("2832408", $player["27715"]["fideID"]);
        $this->assertStringContainsStringIgnoringCase("M", $player["27715"]["gender"]);


    }
}
