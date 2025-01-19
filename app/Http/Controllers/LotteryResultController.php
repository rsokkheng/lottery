<?php

namespace App\Http\Controllers;

use App\Models\LotteryResult;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class LotteryResultController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Permission::orderBy('id','DESC')->get();
        return view('admin.lottery-result.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(LotteryResult $lotteryResult)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LotteryResult $lotteryResult)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LotteryResult $lotteryResult)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LotteryResult $lotteryResult)
    {
        //
    }
}
