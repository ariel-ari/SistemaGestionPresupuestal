<?php

namespace App\Traits;

trait ResetsForm
{
    public function resetForm()
    {
        $this->form->reset();
        $this->resetValidation();
        $this->resetErrorBag();
    }
}