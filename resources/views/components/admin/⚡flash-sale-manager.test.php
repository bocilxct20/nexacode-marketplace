<?php

use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test('admin.flash-sale-manager')
        ->assertStatus(200);
});
