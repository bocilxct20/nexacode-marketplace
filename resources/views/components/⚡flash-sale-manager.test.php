<?php

use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test('flash-sale-manager')
        ->assertStatus(200);
});
