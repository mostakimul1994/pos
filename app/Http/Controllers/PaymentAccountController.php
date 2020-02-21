<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\PaymentAccount;

class PaymentAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('payment_account.view')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = session()->get('user.business_id');

            $payment_accounts = PaymentAccount::where('business_id', $business_id)
                                    ->select(['name', 'type', 'note', 'id']);

            return DataTables::of($payment_accounts)
                            ->addColumn(
                                'action',
                                '@can("payment_account.update")
                                <button data-url="{{action(\'PaymentAccountController@edit\',[$id])}}" class="btn btn-xs btn-primary edit_payment_account"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                                @endcan &nbsp;
                                
                                @can("payment_account.delete")
                                <button data-url="{{action(\'PaymentAccountController@destroy\',[$id])}}" class="btn btn-xs btn-danger delete_payment_account"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                                @endcan'
                            )
                            ->editColumn('type', function (PaymentAccount $pa) {
                                return $pa::account_name($pa->type);
                            })
                            ->removeColumn('id')
                            ->rawColumns([3])
                            ->make(false);
        }
        
        return view('payment_account.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('payment_account.create')) {
            abort(403, 'Unauthorized action.');
        }

        $type = PaymentAccount::account_types();
        return view('payment_account.create')->with(compact('type'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('payment_account.create')) {
            abort(403, 'Unauthorized action.');
        }
        
        if (request()->ajax()) {
            try {
                $input = $request->only(['name', 'type', 'note']);
                $business_id = $request->session()->get('user.business_id');
                
                $payment_accounts = new PaymentAccount;
                $payment_accounts->business_id = $business_id;
                $payment_accounts->name = $input['name'];
                $payment_accounts->type = $input['type'];
                $payment_accounts->note = $input['note'];
                $payment_accounts->created_by = $request->session()->get('user.id');
                $payment_accounts->save();

                $output = ['success' => true,
                            'msg' => __("lang_v1.payment_account_success")
                        ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                    
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                            ];
            }

            return $output;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('payment_account.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $payment_accounts = PaymentAccount::where('business_id', $business_id)
                                                ->find($id);
            $type = PaymentAccount::account_types();
            return view('payment_account.edit')
                ->with(compact('payment_accounts', 'type'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('payment_account.update')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            try {
                $input = $request->only(['name', 'type', 'note']);

                $business_id = request()->session()->get('user.business_id');
                $payment_accounts = PaymentAccount::where('business_id', $business_id)
                                                    ->findOrFail($id);
                $payment_accounts->name = $input['name'];
                $payment_accounts->type = $input['type'];
                $payment_accounts->note = $input['note'];
                $payment_accounts->save();

                $output = ['success' => true,
                                'msg' => __("lang_v1.payment_account_updated_success")
                                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }
            
            return $output;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        if (!auth()->user()->can('payment_account.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = session()->get('user.business_id');
            
                $payment_accounts = PaymentAccount::where('business_id', $business_id)
                                                    ->findOrFail($id);
                $payment_accounts->delete();

                $output = ['success' => true,
                                    'msg' => __("lang_v1.payment_account_deleted_success")
                                    ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => __("messages.something_went_wrong")
                        ];
            }
            
            return $output;
        }
    }
}
