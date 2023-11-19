<?php

namespace App\Http\Controllers;

use App\Models\Transaction;

/**
 * @group Reports
 * APIs for managing reports
 * @OAS\SecurityScheme(
 *      securityScheme="bearer_token",
 *      type="http",
 *      scheme="bearer"
 * )
 */
class ReportController extends Controller
{
    /**
     * Generate a report of transactions with payments.
     *
     * This endpoint provides a report of transactions along with their associated payments.
     *
     * @authenticated
     *
     * @response {
     *   "data": {
     *     "reportData": [
     *       {
     *         "id": 1,
     *         "description": "Transaction 1",
     *         "amount": 100.00,
     *         "payments": [
     *           {
     *             "id": 1,
     *             "amount_paid": 50.00
     *           },
     *           {
     *             "id": 2,
     *             "amount_paid": 50.00
     *           }
     *         ]
     *       },
     *       // Additional transactions...
     *     ]
     *   },
     *   "message": "Report generated successfully.",
     *   "code": 200,
     *   "status": "success"
     * }
     *
     * @return \Illuminate\View\View
     */
    public function generateReport()
    {
        $reportData = Transaction::with('payments')->get();

        return view('reports.generate', compact('reportData'));
    }
}
