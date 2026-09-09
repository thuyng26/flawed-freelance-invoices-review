<?php

it('reports healthy', function () {
    $response = $this->get('/health');

    $response->assertOk()
        ->assertJsonPath('status', 'ok');
});
