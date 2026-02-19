<?php

class ContactFormCest
{
    public function submitEmptyForm(\FunctionalTester $I)
    {
        $I->sendAjaxPostRequest('/api/contact/submit', []);
        $I->seeResponseCodeIs(422);
        $response = $this->decodeJsonResponse($I);
        $I->assertSame(false, $response['success'] ?? null);
        $I->assertSame('Name cannot be blank.', $response['message'] ?? null);
    }

    public function submitFormWithIncorrectEmail(\FunctionalTester $I)
    {
        $I->sendAjaxPostRequest('/api/contact/submit', [
            'name' => 'tester',
            'email' => 'tester.email',
            'subject' => 'test subject',
            'body' => 'test content',
            'verifyCode' => 'testme',
        ]);
        $I->seeResponseCodeIs(422);
        $response = $this->decodeJsonResponse($I);
        $I->assertSame(false, $response['success'] ?? null);
        $I->assertSame('Email is not a valid email address.', $response['message'] ?? null);
    }

    public function submitFormSuccessfully(\FunctionalTester $I)
    {
        $I->sendAjaxPostRequest('/api/contact/submit', [
            'name' => 'tester',
            'email' => 'tester@example.com',
            'subject' => 'test subject',
            'body' => 'test content',
            'verifyCode' => 'testme',
        ]);
        $I->seeResponseCodeIs(200);
        $response = $this->decodeJsonResponse($I);
        $I->assertSame(true, $response['success'] ?? null);
        $I->seeEmailIsSent();
    }

    private function decodeJsonResponse(\FunctionalTester $I): array
    {
        $response = json_decode($I->grabPageSource(), true);

        $I->assertIsArray($response);

        return $response;
    }
}
