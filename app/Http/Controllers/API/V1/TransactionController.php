<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transaction;
use App\User;
use App\Traits\StripeTrait;

class TransactionController extends Controller
{
    use StripeTrait;

    /**
     * This will fetch recent transactions of tippers
     */
    public function transactions()
    {
        $transactions = Transaction::with(['tippe' => function ($q1){
            $q1->select('id', 'first_name', 'last_name', 'profile_img');
        }])->where('tipper_id', auth()->user()->id)->orderBy('created_at', 'desc')->paginate(config('constants.paginationLimit.limit'));

        $transactions->map(function ($item, $key) {
            $item['card'] = $this->__transactionDetails($item->transection_id);
        });

        $transactions = $transactions->toArray();

        $transactions['data'] = $this->__filterTransections($transactions);

        return $response = [
            'transactions' => $transactions,
            'status' => 200,
        ];
    }

    /**
     * This is payouts for tippes
     */
    public function payouts()
    {
        $payouts = Transaction::with(['tipper' => function ($q1){
            $q1->select('id', 'first_name', 'last_name', 'profile_img');
        }])->where('tippe_id', auth()->user()->id)->orderBy('created_at', 'desc')->paginate(config('constants.paginationLimit.limit'));

        $payouts = $payouts->toArray();
        $payouts['data'] = $this->__filterTransections($payouts);

        return $response = [
            'payouts' => $payouts,
            'status' => 200,
        ];
    }

    /**
     * Payouts Tippie's details
     */
    public function payoutsTippie()
    {
        $tippstotal = Transaction::where('tippe_id', auth()->user()->id)->sum('amount');
        $countTipps = Transaction::where('tippe_id', auth()->user()->id)->whereDate('created_at', '>=', date('Y-m-d H:i:s',strtotime('-7 days')))->count();
        $tippeDetails = User::where('id', auth()->user()->id)->get();
        $tippeDetails->push([
            'total' => $tippstotal,
            'stripeBalance' => (empty(auth()->user()->stripe_acc_id)) ? 0 : number_format($this->balance(auth()->user()->stripe_acc_id), 2),
            'tipCount' => $countTipps
        ]);

        return $response = [
            'tippieDetails' => $tippeDetails,
            'status' => 200,
        ];

        return response()->json($response, $response['status']);
    }

    /**
     * This will fetch card detais from transection id
     */
    private function __transactionDetails($transactionId)
    {
        try {
            return $this->retriveCharge($transactionId)->source['last4'];
        } catch(\Throwable $th) {
            return $th->getMessage();
        }
    }

    /**
     * remove time from date.
     */
    private function __fetchDate($dateTime)
    {
        $dateTime = new \DateTime($dateTime);
        return $dateTime->format('Y-m-d');
    }

    /**
     * This will group by dates
     */
    private function __filterTransections($transactions)
    {
        $filterTransactions = [];
        $count = 0;

        foreach ($transactions['data'] as $key => $transaction) {
            if (in_array($this->__fetchDate($transaction['created_at']), array_column($filterTransactions, 'date'))) {
                $filterTransactions[$count]['transactions'][] = $transaction;
            } else {
                $count++;
                $filterTransactions[$count]['date'] = $this->__fetchDate($transaction['created_at']);
                $filterTransactions[$count]['transactions'][] = $transaction;
            }
        }

        return $filterTransactions;
    }
}
