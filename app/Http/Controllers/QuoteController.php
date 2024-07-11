<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $quotes = Quote::with('category')->paginate(10);
        return view('admin.quotes', compact('quotes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            $data = $request->validate([
                'category_id' => 'required',
                'title' => 'required',
                'quote' => 'required',
            ]);
            // return $request->all();
            Quote::create($data);
            // noty()->theme('light')->addSuccess('The operation was successful.');
            return back()->with('success', 'quote Added Successfully');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\quote  $quote
     * @return \Illuminate\Http\Response
     */
    public function show(quote $quote)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\quote  $quote
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return Quote::with('category')->find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\quote  $quote
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'quote_id' =>'required',
            'title' =>'required',
            'quote' =>'required',
        ]);

        $quote = Quote::find($request->quote_id);
        $quote->update($data);

        return back()->with('success', 'quote Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\quote  $quote
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Quote::find($id)->delete();
        return back()->with('success', 'quote Deleted Successfully');
    }
}
