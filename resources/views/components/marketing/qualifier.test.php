<?php

use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test('marketing.qualifier')
        ->assertStatus(200);
});
