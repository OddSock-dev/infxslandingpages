<?php

use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test('pages::landing')
        ->assertStatus(200);
});
