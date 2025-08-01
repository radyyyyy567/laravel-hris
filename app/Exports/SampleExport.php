<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;

class SampleExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect([
            ['John Doe', 'john@doe.com', '123456789'],
            ['Jane Doe', 'jane@doe.com', '987654321'],
        ]);
    }

    public function headings(): array
    {
        return ['name', 'email', 'phone'];
    }
}
