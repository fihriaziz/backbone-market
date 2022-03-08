<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardSettingController extends Controller
{
    public function store()
    {
        $user = Auth::user();
        $categories = Category::all();

        return view ('pages.dashboard-settings', [
            'user' => $user,
            'categories' => $categories
        ]);
    }

    public function account()
    {
        $user = Auth::user();

        return view ('pages.dashboard-account', [
            'user' => $user
        ]);
    }

    public function update(Request $request, $redirect)
    {
        $data = $request->all();

        $item = Auth::user();

        $data['image'] = $request->file('image')->store('assets/profile', 'public');

        $item->update($data);

        return redirect()->route($redirect);
    }

    public function update_store(Request $request, $redirect)
    {
        $store = Auth::user();

        $store->store_status = $request->store_status;
        $store->categories_id = $request->categories_id;
        $store->store_name = $request->store_name;
        $store->save();

        return redirect()->route($redirect);
    }
}
