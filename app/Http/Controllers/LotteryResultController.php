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

    public function createMienNam()
    {
        $createType = 'mien-nam';
        $data = [
            'Giai tam',
            'Giai bay',
            'Giai sau',
            'Giai nam',
            'Giai tu',
            'Giai ba',
            'Giai nhi',
            'Giai nhat',
            'Giai Dac Biet'
        ];
        return view('admin.lottery-result.create', compact('data','createType'));
    }
    public function createMienTrung()
    {
        $createType = 'mien-trung';
        $data = [
            'Giai tam',
            'Giai bay',
            'Giai sau',
            'Giai nam',
            'Giai tu',
            'Giai ba',
            'Giai nhi',
            'Giai nhat',
            'Giai Dac Biet'
        ];
        return view('admin.lottery-result.create', compact('data','createType'));
    }
    public function createMienBac()
    {
        $createType = 'mien-bac';
        $data = [
            'Giai tam',
            'Giai bay',
            'Giai sau',
            'Giai nam',
            'Giai tu',
            'Giai ba',
            'Giai nhi',
            'Giai nhat',
            'Giai Dac Biet'
        ];
        return view('admin.lottery-result.create', compact('data','createType'));
    }
}
