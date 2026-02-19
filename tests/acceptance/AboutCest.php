<?php

class AboutCest
{
    public function ensureThatAboutWorks(AcceptanceTester $I)
    {
        $I->amOnPage('/about');
        $I->see('About', 'h1');
    }
}
