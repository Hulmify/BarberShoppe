<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Service;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $shop = auth()->user()->shop;
        
        if (!$shop) {
            return view('admin.setup_shop');
        }

        $tz = $shop->timezone ?? config('app.timezone');
        $now = Carbon::now($tz);

        $todaysBookings = $shop->bookings()
            ->whereDate('start_time', $now->toDateString())
            ->orderBy('start_time')
            ->get();
            
        // Stats
        $startOfWeek = $now->copy()->startOfWeek();
        $endOfWeek = $now->copy()->endOfWeek();
        
        $weekRevenue = $shop->bookings()
            ->whereBetween('start_time', [$startOfWeek, $endOfWeek])
            ->where('status', 'completed') // Assuming 'completed' means paid/done
            ->sum('total_price');
        
        $potentialRevenue = $shop->bookings()
            ->whereBetween('start_time', [$startOfWeek, $endOfWeek])
            ->whereIn('status', ['confirmed', 'completed'])
            ->sum('total_price');
            
        $totalCustomers = \App\Models\Customer::whereHas('bookings', function($q) use ($shop) {
             $q->where('shop_id', $shop->id);
        })->count();

        return view('admin.dashboard', compact('shop', 'todaysBookings', 'weekRevenue', 'potentialRevenue', 'totalCustomers'));
    }

    public function editShop()
    {
        $shop = auth()->user()->shop;
        return view('admin.shop.edit', compact('shop'));
    }

    public function updateShop(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'slug' => 'required|string|unique:shops,slug,' . auth()->user()->shop->id,
            'custom_domain' => 'nullable|string|unique:shops,custom_domain,' . auth()->user()->shop->id,
            'description' => 'nullable|string',
            'primary_color' => 'nullable|string',
            'currency' => 'nullable|string|size:3',
            'timezone' => 'required|string',
        ]);

        auth()->user()->shop->update($data);
        return back()->with('success', 'Shop updated.');
    }

    public function storeShop(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'slug' => 'required|string|unique:shops',
            'custom_domain' => 'nullable|string|unique:shops',
            'description' => 'nullable|string',
            'primary_color' => 'nullable|string',
            'currency' => 'nullable|string|size:3',
            'timezone' => 'required|string',
        ]);

        auth()->user()->shop()->create($data);
        return redirect()->route('admin.dashboard');
    }

    public function index(Request $request)
    {
        $shop = auth()->user()->shop;
        if (!$shop) return redirect()->route('admin.dashboard');
        
        $query = $shop->services();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $services = $query->paginate(10)->withQueryString();
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $shop = auth()->user()->shop;
        
        $data = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:5',
        ]);

        $shop->services()->create($data);
        return redirect()->route('admin.services.index')->with('success', 'Service created successfully.');
    }

    public function edit(string $id)
    {
        $service = auth()->user()->shop->services()->findOrFail($id);
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, string $id)
    {
        $service = auth()->user()->shop->services()->findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:5',
        ]);

        $service->update($data);
        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(string $id)
    {
        $service = auth()->user()->shop->services()->findOrFail($id);
        $service->delete();
        
        return back()->with('success', 'Service deleted.');
    }

    public function toggleOffDay()
    {
        $shop = auth()->user()->shop;
        $tz = $shop->timezone ?? config('app.timezone');
        $today = Carbon::now($tz)->toDateString();
        
        if ($shop->off_date && $shop->off_date == $today) {
            $shop->off_date = null;
            $message = 'Shop is now open for today!';
        } else {
            $shop->off_date = $today;
            $message = 'Shop is now closed for today!';
        }
        
        $shop->save();
        
        return back()->with('success', $message);
    }
}
