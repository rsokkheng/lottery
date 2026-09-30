<?php

namespace App\Http\Controllers;

use App\Models\BetReceipt;
use App\Models\BetReceiptUSD;
use App\Models\PosPrintJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Print queue between a phone and the Mini POS (built-in printer).
 * Phone sends a job -> POS Station page (logged in as the same account) prints it.
 */
class PosPrintController extends Controller
{
    // Phone: queue a receipt for the Mini POS
    public function store(Request $request)
    {
        $data = $request->validate([
            'receipt_no' => 'required|string|max:100',
            'currency' => 'required|in:VND,USD',
            'reprint' => 'nullable|boolean',
        ]);

        $user = Auth::user();

        if (!$user->currencies()->where('currency', $data['currency'])->exists()) {
            return response()->json(['success' => false, 'message' => 'Currency not allowed.'], 403);
        }

        $receiptModel = $data['currency'] === 'USD' ? BetReceiptUSD::class : BetReceipt::class;
        if (!$receiptModel::query()->where('receipt_no', $data['receipt_no'])->exists()) {
            return response()->json(['success' => false, 'message' => 'Receipt not found.'], 404);
        }

        $job = PosPrintJob::create([
            'user_id' => $user->id,
            'receipt_no' => $data['receipt_no'],
            'currency' => $data['currency'],
            'is_reprint' => $request->boolean('reprint'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sent to Mini POS.',
            'job_id' => $job->id,
        ]);
    }

    // Mini POS: station page that waits for jobs and prints them
    public function station()
    {
        return view('pos.station', [
            'pending' => $this->pendingQuery()->count(),
        ]);
    }

    // Mini POS: take the oldest waiting job (each job is printed only once)
    public function next()
    {
        foreach ($this->pendingQuery()->orderBy('id')->limit(5)->get() as $job) {
            $claimed = PosPrintJob::query()
                ->whereKey($job->id)
                ->where('status', 'pending')
                ->update(['status' => 'printed', 'printed_at' => now()]);

            if ($claimed) {
                return response()->json([
                    'job' => [
                        'id' => $job->id,
                        'receipt_no' => $job->receipt_no,
                        'url' => $job->receiptUrl(),
                    ],
                ]);
            }
        }

        return response()->json(['job' => null]);
    }

    private function pendingQuery()
    {
        return PosPrintJob::query()
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            // Do not print old jobs when the station was switched off for a long time
            ->where('created_at', '>=', now()->subMinutes(config('pos.station_job_ttl')));
    }
}
