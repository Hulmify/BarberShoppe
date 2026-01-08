<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Stylist;
use Illuminate\Support\Facades\Storage;

class StylistController extends Controller
{
    private function getShop()
    {
        return auth()->user()->shop;
    }

    public function index()
    {
        $shop = $this->getShop();
        $stylists = $shop->stylists;
        return view('admin.stylists.index', compact('shop', 'stylists'));
    }

    public function create()
    {
        $shop = $this->getShop();
        if ($shop->stylists()->count() >= 10) {
            return redirect()->route('admin.stylists.index')->with('error', 'You can only have up to 10 stylists.');
        }
        return view('admin.stylists.create');
    }

    public function store(Request $request)
    {
        $shop = $this->getShop();
        if ($shop->stylists()->count() >= 10) {
            return redirect()->route('admin.stylists.index')->with('error', 'You can only have up to 10 stylists.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'display_order' => 'integer'
        ]);

        $data = $validated;
        unset($data['image']);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageContent = file_get_contents($image->getRealPath());
            $base64 = 'data:' . $image->getMimeType() . ';base64,' . base64_encode($imageContent);
            $data['image_base64'] = $base64;
        }

        $data['shop_id'] = $shop->id;
        $data['is_active'] = $request->has('is_active');
        
        Stylist::create($data);

        return redirect()->route('admin.stylists.index')->with('success', 'Stylist added successfully.');
    }

    public function edit(Stylist $stylist)
    {
        $this->authorizeStylist($stylist);
        return view('admin.stylists.edit', compact('stylist'));
    }

    public function update(Request $request, Stylist $stylist)
    {
        $this->authorizeStylist($stylist);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'display_order' => 'integer'
        ]);

        $data = $validated;
        unset($data['image']);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageContent = file_get_contents($image->getRealPath());
            $base64 = 'data:' . $image->getMimeType() . ';base64,' . base64_encode($imageContent);
            $data['image_base64'] = $base64;
        }

        $data['is_active'] = $request->has('is_active');

        $stylist->update($data);

        return redirect()->route('admin.stylists.index')->with('success', 'Stylist updated successfully.');
    }

    public function destroy(Stylist $stylist)
    {
        $this->authorizeStylist($stylist);
        
        $stylist->delete();

        return redirect()->route('admin.stylists.index')->with('success', 'Stylist deleted successfully.');
    }

    private function authorizeStylist(Stylist $stylist)
    {
        if ($stylist->shop_id !== auth()->user()->shop->id) {
            abort(403);
        }
    }
}
