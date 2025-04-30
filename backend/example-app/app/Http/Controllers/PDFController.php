<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use PDF;

class PDFController extends Controller
{
    public function downloadPDF()
    {
        $expenses = Expense::with('group')->get();

        $pdf = PDF::loadView('pdf.expenses', compact('expenses'));
        return $pdf->download('expenses.pdf');
    }
}
