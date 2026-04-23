<?php

use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test('marketing.product-lead')
        ->assertStatus(200);
});
