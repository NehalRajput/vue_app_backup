<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helper\ApiResponse;
use App\Exports\ExpensesExport;
use Maatwebsite\Excel\Facades\Excel;

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
        $fileName = 'expenses.csv';
        return Excel::download(new ExpensesExport, $fileName, \Maatwebsite\Excel\Excel::CSV);
    }
}
