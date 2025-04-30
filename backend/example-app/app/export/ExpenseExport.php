<?php

namespace App\Exports;

use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExpensesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Expense::where('user_id', Auth::id())
            ->with('group') 
            ->get()
            ->map(function ($expense) {
                return [
                    'Title' => $expense->expense_name,
                    'Amount' => $expense->amount,
                    'Date' => $expense->expense_date,
                    'Group Name' => $expense->group->name ?? 'N/A',
                ];
            });
    }

    public function headings(): array
    {
        return ['Title', 'Amount', 'Date', 'Group Name'];
    }
}
