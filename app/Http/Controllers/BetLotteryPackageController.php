<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\BetLotteryPackage;
use Illuminate\Support\Facades\Auth;
use App\Models\BetLotteryPackageConfiguration;

class BetLotteryPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = BetLotteryPackage::with('packageConfiges')->orderBy('id','DESC')->get();
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

    public function edit($category)
    {
        $data = BetLotteryPackageConfiguration::where('id',decrypt($category))->first();
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
