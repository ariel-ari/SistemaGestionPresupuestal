<?php

namespace App\Livewire\Forms;

use App\Models\Subclassifier;
use Illuminate\Validation\Rule;

class SubclassifierForm extends BaseForm
{
    public ?int $classifier_id = null;

    public ?string $code = '';

    public ?string $name = '';

    public bool $is_active = true;

    public function modelClass(): string
    {
        return Subclassifier::class;
    }

    public function rules(): array
    {
        $id = $this->model?->id;

        return [
            'classifier_id' => ['required', 'integer', Rule::exists('classifiers', 'id')->whereNull('deleted_at')],
            'code' => ['required', 'string', 'min:3', 'max:255', Rule::unique('subclassifiers', 'code')->ignore($id)->whereNull('deleted_at')],
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }

    public function normalizeData(): void
    {
        $this->code = mb_convert_case(trim($this->code), MB_CASE_UPPER, 'UTF-8');
        $this->name = mb_convert_case(trim($this->name), MB_CASE_TITLE, 'UTF-8');
    }

    public function getData(): array
    {
        $data = $this->only([
            'classifier_id',
            'code',
            'name',
            'is_active',
        ]);

        return $data;
    }
}
