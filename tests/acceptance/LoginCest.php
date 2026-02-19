<?php

class LoginCest
{
    public function ensureThatLoginWorks(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->see('Login', 'h1');

        $I->amGoingTo('try to login with correct credentials');
        $I->fillField('#username', 'admin');
        $I->fillField('#password', 'admin');
        $I->click('Login');
        $I->wait(1);

        $I->expectTo('see user info');
        $I->see('Logout (admin)');
    }
}
