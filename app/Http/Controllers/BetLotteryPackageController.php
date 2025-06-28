<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\BetLotteryPackage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\BetLotteryPackageConfiguration;

class BetLotteryPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = BetLotteryPackage::with('packageConfiges')->orderBy('id','ASC')->get();
        return view('admin.bet-lottery-package.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.bet-lottery-package.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|max:255',
        ]);
        $baseSlug = Str::slug($request->name);
        $uniqueSlug = $baseSlug;
        $counter = 1;
        while (BetLotteryPackage::where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = $baseSlug . '-' . $counter;
            $counter++;
        }
        BetLotteryPackage::create([
            'name'=>$request->name,
            'slug'=>$uniqueSlug,
        ]);
        return redirect()->route('admin.bet-lottery-package.index')->with('success','Category created successfully.');
    }

    public function edit($id)
    {
        $data = DB::table('bet_package_configurations')
        ->join('bet_lottery_packages', 'bet_package_configurations.package_id', '=', 'bet_lottery_packages.id')
        ->select(
            'bet_package_configurations.*',
            'bet_lottery_packages.package_code as package_code' // example column
        )
        ->where('bet_package_configurations.id', '=', decrypt($id))
        ->first(); 
        return view('admin.bet-lottery-package.edit',compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        BetLotteryPackageConfiguration::where('id', $request->id)->update([
            'rate' => $request->rate,
            'price' => $request->price,
            'updated_at' => now(),
            'updated_by' => Auth::user()->id??0,
        ]);
        return redirect()->route('admin.bet-lottery-package.index')->with('success','Package updated successfully.');   
    }

    public function destroy($id)
    {
        BetLotteryPackage::where('id',decrypt($id))->delete();
        return redirect()->route('admin.bet-lottery-package.index')->with('error','Category deleted successfully.');   
    }
}
