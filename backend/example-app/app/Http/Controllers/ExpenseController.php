<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helper\ApiResponse;
use App\Exports\ExpenseExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Exception;

class ExpenseController extends Controller
{
    public function index()
    {
        try {
            $expenses = Expense::where('user_id', Auth::id())->with('group')->get();
            return ApiResponse::success($expenses, 'Expenses retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse::error('Failed to retrieve expenses', 500, $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'expense_name' => 'required|string|max:255',
                'amount'       => 'required|integer',
                'expense_date' => 'required|date',
                'group_id'     => 'required|exists:groups,id',
            ]);

            $data['user_id'] = Auth::id();
            $expense = Expense::create($data);

            return ApiResponse::success($expense, 'Expense created successfully');
        } catch (Exception $e) {
            return ApiResponse::error('Failed to create expense', 500, $e->getMessage());
        }
    }

    public function show(Expense $expense)
    {
        try {
            if ($expense->user_id !== Auth::id()) {
                return ApiResponse::error('Unauthorized', 403);
            }

            return ApiResponse::success($expense, 'Expense retrieved');
        } catch (Exception $e) {
            return ApiResponse::error('Failed to retrieve expense', 500, $e->getMessage());
        }
    }

    public function update(Request $request, Expense $expense)
    {
        try {
            if ($expense->user_id !== Auth::id()) {
                return ApiResponse::error('Unauthorized', 403);
            }

            $data = $request->validate([
                'expense_name' => 'required|string|max:255',
                'amount'       => 'required|numeric',
                'expense_date' => 'required|date',
                'group_id'     => 'required|exists:groups,id',
            ]);

            $expense->update($data);

            return ApiResponse::success($expense, 'Expense updated successfully');
        } catch (Exception $e) {
            return ApiResponse::error('Failed to update expense', 500, $e->getMessage());
        }
    }

    public function destroy(Expense $expense)
    {
        try {
            if ($expense->user_id !== Auth::id()) {
                return ApiResponse::error('Unauthorized', 403);
            }

            $expense->delete();

            return ApiResponse::success([], 'Expense deleted successfully');
        } catch (Exception $e) {
            return ApiResponse::error('Failed to delete expense', 500, $e->getMessage());
        }
    }

    public function export()
    {
        try {
            $userId = auth()->id();

            $expenseCount = Expense::where('user_id', $userId)->count();
            if ($expenseCount === 0) {
                return ApiResponse::error('No expenses found for export');
            }

            $fileName = 'expenses-' . now()->format('Y-m-d-H-i-s') . '.csv';
            return Excel::download(new ExpenseExport($userId), $fileName);
        } catch (Exception $e) {
            Log::error('CSV Export failed: ' . $e->getMessage());
            return ApiResponse::error('Failed to export expenses', 500, $e->getMessage());
        }
    }

    public function exportPdf()
    {
        try {
            $userId = auth()->id();

            $expenses = Expense::where('user_id', $userId)
                ->with('group')
                ->get();

            if ($expenses->isEmpty()) {
                return ApiResponse::error('No expenses found for PDF export', 400);
            }

            $pdf = Pdf::loadView('expenses.pdf', compact('expenses'));
            return $pdf->download('expenses-' . now()->format('Y-m-d-H-i-s') . '.pdf');
        } catch (Exception $e) {
            Log::error('PDF Export failed: ' . $e->getMessage());
            return ApiResponse::error('Failed to export PDF', 500, $e->getMessage());
        }
    }
}
