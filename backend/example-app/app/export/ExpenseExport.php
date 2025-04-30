<?php
namespace App\Exports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExpensesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Expense::select('id', 'name', 'amount', 'date')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Amount', 'Date'];
    }
}
