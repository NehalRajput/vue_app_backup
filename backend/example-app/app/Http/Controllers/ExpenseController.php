<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helper\ApiResponse;
use App\Exports\ExpenseExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::where('user_id', Auth::id())->with('group')->get();
        return ApiResponse::success($expenses, 'Expenses retrieved successfully');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'expense_name' => 'required|string|max:255',
            'amount'       => 'required|integer',
            'expense_date' => 'required|date',
            'group_id'     => 'required|exists:groups,id',
        ]);

        $data['user_id'] = Auth::id();
        $expense = Expense::create($data);

        return ApiResponse::success($expense, 'Expense created successfully');
    }

    public function show(Expense $expense)
    {
        if ($expense->user_id !== Auth::id()) {
            return ApiResponse::error('Unauthorized');
        }

        return ApiResponse::success($expense, 'Expense retrieved');
    }

    public function update(Request $request, Expense $expense)
    {
        if ($expense->user_id !== Auth::id()) {
            return ApiResponse::error('Unauthorized');
        }

        $data = $request->validate([
            'expense_name' => 'required|string|max:255',
            'amount'       => 'required|numeric',
            'expense_date' => 'required|date',
            'group_id'     => 'required|exists:groups,id',
        ]);

        $expense->update($data);

        return ApiResponse::success($expense, 'Expense updated successfully');
    }

    public function destroy(Expense $expense)
    {
        if ($expense->user_id !== Auth::id()) {
            return ApiResponse::error('Unauthorized');
        }

        $expense->delete();

        return ApiResponse::success([], 'Expense deleted successfully');
    }

  
    public function export()
    {
        try {
            // Get the authenticated user's ID
            $userId = auth()->id();

            // Check if user has expenses
            $expenseCount = Expense::where('user_id', $userId)->count();
            if ($expenseCount === 0) {
                return ApiResponse::error('No expenses found for export');
            }

            // Generate a filename with timestamp
            $fileName = 'expenses-' . now()->format('Y-m-d-H-i-s') . '.csv';

            // Return the download response
            return Excel::download(new ExpenseExport($userId), $fileName);
        } catch (\Exception $e) {
            // Log the error
            Log::error('CSV Export failed: ' . $e->getMessage());

            // Return an error message
            return ApiResponse::error('Failed to export expenses: ' . $e->getMessage());
        }
    }



    public function exportPdf()
    {
        // Get the authenticated user's ID
        $userId = auth()->id();

        // Fetch the user's expenses along with group data
        $expenses = Expense::where('user_id', $userId)
            ->with('group')
            ->get();

        // Check if the user has expenses
        if ($expenses->isEmpty()) {
            return response()->json(['error' => 'No expenses found'], 400);
        }

        // Load a view with the data and generate the PDF
        $pdf = Pdf::loadView('expenses.pdf', compact('expenses'));

        // Return the PDF download response
        return $pdf->download('expenses-' . now()->format('Y-m-d-H-i-s') . '.pdf');
    }

}
