<?php

use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test('pages::products.show')
        ->assertStatus(200);
});
