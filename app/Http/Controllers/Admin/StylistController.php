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
        
        $stylist = Stylist::create($data);

        // Create default availability for new stylist
        for ($i = 0; $i <= 6; $i++) {
            $stylist->availabilities()->create([
                'day_of_week' => $i,
                'shop_id' => $shop->id,
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'is_active' => true
            ]);
        }

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

    public function availability(Stylist $stylist)
    {
        $this->authorizeStylist($stylist);
        
        // Ensure all days exist for this stylist
        for ($i = 0; $i <= 6; $i++) {
            $stylist->availabilities()->firstOrCreate(
                ['day_of_week' => $i, 'shop_id' => $stylist->shop_id],
                ['start_time' => '09:00:00', 'end_time' => '17:00:00', 'is_active' => true]
            );
        }
        
        $availabilities = $stylist->availabilities()->orderBy('day_of_week')->get();
        
        return view('admin.stylists.availability', compact('stylist', 'availabilities'));
    }

    public function updateAvailability(Request $request, Stylist $stylist)
    {
        $this->authorizeStylist($stylist);
        
        $data = $request->validate([
            'schedule' => 'required|array',
            'schedule.*.start_time' => 'required|date_format:H:i',
            'schedule.*.end_time' => 'required|date_format:H:i',
            'schedule.*.is_active' => 'nullable',
        ]);
        
        foreach ($data['schedule'] as $day => $times) {
            $stylist->availabilities()->where('day_of_week', $day)->update([
                'start_time' => $times['start_time'],
                'end_time' => $times['end_time'],
                'is_active' => isset($times['is_active'])
            ]);
        }
        
        return back()->with('success', 'Stylist schedule updated successfully.');
    }

    private function authorizeStylist(Stylist $stylist)
    {
        if ($stylist->shop_id !== auth()->user()->shop->id) {
            abort(403);
        }
    }
}
