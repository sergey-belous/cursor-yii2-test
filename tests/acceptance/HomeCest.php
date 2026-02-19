<?php

class HomeCest
{
    public function ensureThatHomePageWorks(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->see('URL Shortener');
        $I->see('Сервис коротких ссылок + QR');

        $I->seeLink('About');
        $I->click('About');
        $I->wait(1);

        $I->see('Это SPA-версия сервиса коротких ссылок на Vue 3.');
    }
}
