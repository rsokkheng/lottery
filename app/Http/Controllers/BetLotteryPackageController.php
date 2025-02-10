<?php

namespace App\Http\Controllers;

use App\Models\BetLotteryPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BetLotteryPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = BetLotteryPackage::orderBy('id','DESC')->get();
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
        $data = BetLotteryPackage::where('id',decrypt($category))->first();
        return view('admin.bet-lottery-package.edit',compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'name'=>'required|max:255',
        ]);
        $baseSlug = Str::slug($request->name);
        $uniqueSlug = $baseSlug;
        $counter = 1;
        
        while (BetLotteryPackage::where('slug', $uniqueSlug)->where('id', '!=', $request->id)->exists()) {
            $uniqueSlug = $baseSlug . '-' . $counter;
            $counter++;
        }

        BetLotteryPackage::where('id', $request->id)->update([
            'name' => $request->name,
            'slug' => $uniqueSlug,
        ]);
        return redirect()->route('admin.bet-lottery-package.index')->with('info','Category updated successfully.');   
    }

    public function destroy($id)
    {
        BetLotteryPackage::where('id',decrypt($id))->delete();
        return redirect()->route('admin.bet-lottery-package.index')->with('error','Category deleted successfully.');   
    }
}
