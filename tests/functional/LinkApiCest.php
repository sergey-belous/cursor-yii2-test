<?php

class LinkApiCest
{
    public function createFailsWithInvalidUrl(\FunctionalTester $I)
    {
        $I->sendAjaxPostRequest('/api/link/create', [
            'url' => 'not-a-valid-url',
        ]);
        $I->seeResponseCodeIs(422);
        $response = json_decode($I->grabPageSource(), true);

        $I->assertIsArray($response);
        $I->assertSame(false, $response['success'] ?? null);
        $I->assertSame('Введите корректный URL (http/https).', $response['message'] ?? null);
    }
}
