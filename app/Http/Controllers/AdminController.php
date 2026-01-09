<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Service;
use App\Models\Customer;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Display the main admin dashboard with today's overview and stats.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $shop = auth()->user()->shop;
        
        if (!$shop) {
            return view('admin.setup_shop');
        }

        $tz = $shop->timezone ?? config('app.timezone');
        config(['app.timezone' => $tz]);
        date_default_timezone_set($tz);
        
        $now = Carbon::now();

        // Fetch bookings for the local 'Today' by calculating UTC boundaries
        $startOfDay = $now->copy()->startOfDay()->setTimezone('UTC');
        $endOfDay = $now->copy()->endOfDay()->setTimezone('UTC');

        $allTodaysBookings = $shop->bookings()
            ->whereBetween('start_time', [$startOfDay, $endOfDay])
            ->with(['customer', 'items.service', 'stylist'])
            ->get();
        
        $stylists = $shop->stylists()->where('is_active', true)->get();

        // Shift all bookings to the shop's timezone for accurate display and diffs
        $allTodaysBookings->each(function($b) use ($tz) {
            $b->start_time->setTimezone($tz);
            $b->end_time->setTimezone($tz);
        });

        $ongoingBookings = $allTodaysBookings->filter(function($b) use ($now) {
            return $b->status === 'in_progress' || 
                   ($b->status !== 'completed' && $b->status !== 'cancelled' && $now->between($b->start_time, $b->end_time));
        })->sortBy('start_time');

        $ongoingIds = $ongoingBookings->pluck('id')->toArray();

        $upcomingBookings = $allTodaysBookings->filter(function($b) use ($now, $ongoingIds) {
            return $b->status !== 'completed' && 
                   $b->status !== 'cancelled' && 
                   $b->start_time->gt($now) && 
                   !in_array($b->id, $ongoingIds);
        })->sortBy('start_time');

        $pastBookings = $allTodaysBookings->filter(function($b) use ($now) {
            return $b->status === 'completed' || ($b->status !== 'cancelled' && $b->end_time->lt($now));
        })->sortByDesc('start_time');

        $todaysBookings = $ongoingBookings->concat($upcomingBookings)->concat($pastBookings);
            
        // Stats
        $startOfWeek = $now->copy()->startOfWeek()->setTimezone('UTC');
        $endOfWeek = $now->copy()->endOfWeek()->setTimezone('UTC');
        
        $weekRevenue = $shop->bookings()
            ->whereBetween('start_time', [$startOfWeek, $endOfWeek])
            ->where('status', 'completed')
            ->sum('total_price');
        
        $potentialRevenue = $shop->bookings()
            ->whereBetween('start_time', [$startOfWeek, $endOfWeek])
            ->whereIn('status', ['confirmed', 'completed', 'in_progress'])
            ->sum('total_price');
            
        $totalCustomers = Customer::whereHas('bookings', function($q) use ($shop) {
             $q->where('shop_id', $shop->id);
        })->count();

        return view('admin.dashboard', compact(
            'shop', 
            'todaysBookings', 
            'ongoingBookings', 
            'upcomingBookings', 
            'pastBookings', 
            'weekRevenue', 
            'potentialRevenue', 
            'totalCustomers', 
            'now',
            'tz',
            'stylists'
        ));
    }

    /**
     * Show the shop settings edit form.
     *
     * @return \Illuminate\View\View
     */
    public function editShop()
    {
        $shop = auth()->user()->shop;
        return view('admin.shop.edit', compact('shop'));
    }

    /**
     * Update the shop settings.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
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
            'logo' => 'nullable|image|max:2048',
            'remove_logo' => 'nullable|boolean',
        ]);

        if ($request->hasFile('logo')) {
            $image = $request->file('logo');
            $imageContent = file_get_contents($image->getRealPath());
            $base64 = 'data:' . $image->getMimeType() . ';base64,' . base64_encode($imageContent);
            $data['logo'] = $base64;
        } elseif ($request->boolean('remove_logo')) {
            $data['logo'] = null;
        }

        unset($data['remove_logo']);
        auth()->user()->shop->update($data);
        return back()->with('success', 'Shop updated.');
    }

    /**
     * Create a new shop for the user.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
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
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $image = $request->file('logo');
            $imageContent = file_get_contents($image->getRealPath());
            $base64 = 'data:' . $image->getMimeType() . ';base64,' . base64_encode($imageContent);
            $data['logo'] = $base64;
        }

        auth()->user()->shop()->create($data);
        return redirect()->route('admin.dashboard');
    }

    /**
     * List all services for the shop.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
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

    /**
     * Show form to create a new service.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.services.create');
    }

    /**
     * Store a new service.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Show form to edit an existing service.
     *
     * @param string $id
     * @return \Illuminate\View\View
     */
    public function edit(string $id)
    {
        $service = auth()->user()->shop->services()->findOrFail($id);
        return view('admin.services.edit', compact('service'));
    }

    /**
     * Update an existing service.
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Delete a service.
     *
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        $service = auth()->user()->shop->services()->findOrFail($id);
        $service->delete();
        
        return back()->with('success', 'Service deleted.');
    }

    /**
     * Toggle "Closed Today" status for the shop.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
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

