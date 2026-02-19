<?php

class ContactCest
{
    public function _before(\AcceptanceTester $I)
    {
        $I->amOnPage('/contact');
    }

    public function contactPageWorks(AcceptanceTester $I)
    {
        $I->wantTo('ensure that contact page works');
        $I->see('Contact', 'h1');
    }

    public function contactFormCanBeSubmitted(AcceptanceTester $I)
    {
        $I->amGoingTo('submit contact form with correct data');
        $I->fillField('#contact-name', 'tester');
        $I->fillField('#contact-email', 'tester@example.com');
        $I->fillField('#contact-subject', 'test subject');
        $I->fillField('#contact-body', 'test content');
        $I->fillField('#contact-verify-code', 'testme');

        $I->click('Submit');
        $I->wait(1);

        $I->see('Thank you for contacting us. We will respond to you as soon as possible.');
    }
}
