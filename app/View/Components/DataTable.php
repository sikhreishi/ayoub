<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DataTable extends Component
{
    public function __construct(
        public string $title,
        public string $fetchUrl,
        public string $tableId = 'dataTable',
        public array $columns = [],
        public ?string $createUrl = null,
        public array $columnsConfig = []
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.data-table');
    }
}
