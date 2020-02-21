<?php

namespace App\Console\Commands;

use App\Business;
use App\Transaction;
use App\TransactionSellLinesPurchaseLines;
use App\PurchaseLine;

use Illuminate\Console\Command;

use App\Utils\TransactionUtil;
use DB;

class MapPurchaseSell extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:mapPurchaseSell';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete existing mapping and Add mapping for purchase & Sell for all transactions of all businesses.';

    protected $transactionUtil;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil)
    {
        parent::__construct();

        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', '512M');

            DB::beginTransaction();

            //Delete existing mapping and sold quantity.
            DB::table('transaction_sell_lines_purchase_lines')->delete();

            PurchaseLine::whereNotNull('created_at')
                ->update(['quantity_sold' => 0]);

            //Get all business
            $businesses = Business::all();

            foreach ($businesses as $business) {
                //Get all transactions
                $transactions = Transaction::where('business_id', $business->id)
                                    ->where('type', 'sell')
                                    ->where('status', 'final')
                                    ->orderBy('created_at', 'asc')
                                    ->get();

                //Iterate through all transaction and add mapping.
                foreach ($transactions as $transaction) {
                    $business_formatted = ['id' => $business->id,
                                'accounting_method' => $business->accounting_method,
                                'location_id' => $transaction->location_id
                            ];

                    $this->transactionUtil->mapPurchaseSell($business_formatted, $transaction->sell_lines, 'purchase', false);
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            die($e->getMessage());
        }
    }
}
