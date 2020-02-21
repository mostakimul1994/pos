<?php

namespace App\Http\Controllers;

use App\VariationTemplate;
use App\VariationValueTemplate;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class VariationTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $variations = VariationTemplate::where('business_id', $business_id)
                        ->with(['values']);

            return Datatables::of($variations)
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'VariationTemplateController@edit\', [$id])}}" class="btn btn-xs btn-primary edit_variation_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        &nbsp;
                        <button data-href="{{action(\'VariationTemplateController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_variation_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>'
                )
                ->editColumn('values', function ($data) {
                    $values_arr = [];
                    foreach ($data->values as $attr) {
                        $values_arr[] = $attr->name;
                    }
                    return implode(', ', $values_arr);
                })
                ->removeColumn('id')
                ->removeColumn('business_id')
                ->removeColumn('created_at')
                ->removeColumn('updated_at')
                ->rawColumns([2])
                ->make(false);
        }

        return view('variation.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('variation.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $input = $request->only(['name']);
            $input['business_id'] = $request->session()->get('user.business_id');
            $variation = VariationTemplate::create($input);
            
            //craete variation values
            if (!empty($request->input('variation_values'))) {
                $values = $request->input('variation_values');
                $data = [];
                foreach ($values as $value) {
                    if (!empty($value)) {
                        $data[] = [ 'name' => $value];
                    }
                }
                $variation->values()->createMany($data);
            }
            
            $output = ['success' => true,
                            'data' => $variation,
                            'msg' => 'Variation added succesfully'
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                            'msg' => 'Something went wrong, please try again'
                        ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\VariationTemplate  $variationTemplate
     * @return \Illuminate\Http\Response
     */
    public function show(VariationTemplate $variationTemplate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $variation = VariationTemplate::where('business_id', $business_id)
                            ->with(['values'])->find($id);

            return view('variation.edit')
                ->with(compact('variation'));
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
        if (request()->ajax()) {
            try {
                $input = $request->only(['name']);
                $business_id = $request->session()->get('user.business_id');

                $variation = VariationTemplate::where('business_id', $business_id)->findOrFail($id);
                $variation->name = $input['name'];
                $variation->save();
                
                //update variation
                if (!empty($request->input('variation_values'))) {
                    $values = $request->input('variation_values');
                    $data = [];
                    foreach ($values as $value) {
                        if (!empty($value)) {
                            $data[] = new VariationValueTemplate([ 'name' => $value]);
                        }
                    }
                    $variation->values()->delete();
                    $variation->values()->saveMany($data);
                }

                $output = ['success' => true,
                            'msg' => 'Variation updated succesfully'
                            ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => 'Something went wrong, please try again'
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
        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                $variation = VariationTemplate::where('business_id', $business_id)->findOrFail($id);
                $variation->delete();

                $output = ['success' => true,
                            'msg' => 'Category deleted succesfully'
                            ];
            } catch (\Eexception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
                $output = ['success' => false,
                            'msg' => 'Something went wrong, please try again'
                        ];
            }

            return $output;
        }
    }
}
