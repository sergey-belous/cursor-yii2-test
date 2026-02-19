<?php

class LoginFormCest
{
    public function meReturnsGuestByDefault(\FunctionalTester $I)
    {
        $I->sendAjaxGetRequest('/api/auth/me');
        $I->seeResponseCodeIs(200);
        $response = $this->decodeJsonResponse($I);

        $I->assertSame(false, $response['authenticated'] ?? null);
        $I->assertSame(null, $response['user'] ?? null);
    }

    public function loginWithEmptyCredentials(\FunctionalTester $I)
    {
        $I->sendAjaxPostRequest('/api/auth/login', []);
        $I->seeResponseCodeIs(422);
        $response = $this->decodeJsonResponse($I);

        $I->assertSame(false, $response['success'] ?? null);
        $I->assertSame('Username cannot be blank.', $response['message'] ?? null);
    }

    public function loginSuccessfully(\FunctionalTester $I)
    {
        $I->sendAjaxPostRequest('/api/auth/login', [
            'username' => 'admin',
            'password' => 'admin',
            'rememberMe' => true,
        ]);
        $I->seeResponseCodeIs(200);
        $response = $this->decodeJsonResponse($I);
        $I->assertSame(true, $response['success'] ?? null);
        $I->assertSame('admin', $response['user']['username'] ?? null);

        $I->sendAjaxGetRequest('/api/auth/me');
        $I->seeResponseCodeIs(200);
        $response = $this->decodeJsonResponse($I);
        $I->assertSame(true, $response['authenticated'] ?? null);
        $I->assertSame('admin', $response['user']['username'] ?? null);

        $I->sendAjaxPostRequest('/api/auth/logout', []);
        $I->seeResponseCodeIs(200);
        $response = $this->decodeJsonResponse($I);
        $I->assertSame(true, $response['success'] ?? null);

        $I->sendAjaxGetRequest('/api/auth/me');
        $I->seeResponseCodeIs(200);
        $response = $this->decodeJsonResponse($I);
        $I->assertSame(false, $response['authenticated'] ?? null);
    }

    private function decodeJsonResponse(\FunctionalTester $I): array
    {
        $response = json_decode($I->grabPageSource(), true);

        $I->assertIsArray($response);

        return $response;
    }
}
