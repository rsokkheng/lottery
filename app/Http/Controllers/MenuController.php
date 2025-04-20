<?php
namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::all();
        return view('admin.menus.index', compact('menus'));
    }

    public function create()
    {
        return view('admin.menus.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'text' => 'nullable|string',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('banner')) {
            $bannerFile = $request->file('banner');
            $bannerPath = 'uploads/banners';
            $bannerName = time() . '_' . $bannerFile->getClientOriginalName();
            $bannerFile->move(public_path($bannerPath), $bannerName);
            $data['banner'] = $bannerName;
        }
        
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imagePath = 'uploads/images';
            $imageName = time() . '_' . $imageFile->getClientOriginalName();
            $imageFile->move(public_path($imagePath), $imageName);
            $data['image'] = $imageName;
        }
        

        Menu::create($data);

        return redirect()->route('admin.menu.index')->with('success', 'Menu created successfully.');
    }

    public function show(Menu $menu)
    {
        return view('admin.menus.show', compact('menu'));
    }

    public function edit($id)
    {
        $betMenu = Menu::where('id',decrypt($id))->first();
        return view('admin.menus.edit',compact('betMenu'));
    }

    public function update(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'text' => 'nullable|string',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        // Handle Banner Upload
        if ($request->hasFile('banner')) {
            $bannerFile = $request->file('banner');
            $bannerPath = 'uploads/banners';
            $bannerName = time() . '_' . $bannerFile->getClientOriginalName();
            $bannerFile->move(public_path($bannerPath), $bannerName);
            $data['banner'] =  $bannerName;
        }
    
        // Handle Image Upload
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imagePath = 'uploads/images';
            $imageName = time() . '_' . $imageFile->getClientOriginalName();
            $imageFile->move(public_path($imagePath), $imageName);
            $data['image'] =  $imageName;
        }
    
        // Update the menu record
        $menu->update($data);
    
        return redirect()->route('admin.menu.index')->with('success', 'Menu updated successfully.');
    }
    

    public function destroy($id)
    {
        Menu::where('id',decrypt($id))->delete();
        return redirect()->route('admin.menu.index')->with('success', 'Menu deleted successfully.');
    }
}

    