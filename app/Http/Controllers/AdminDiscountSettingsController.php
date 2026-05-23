<?php

namespace App\Http\Controllers;

use App\Models\DiscountSetting;
use Illuminate\Http\Request;

class AdminDiscountSettingsController extends Controller
{
    public function edit()
    {
        $setting = DiscountSetting::current();

        return view('admin.settings.discounts', compact('setting'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'second_child_percentage' => 'required|integer|min:0|max:100',
            'third_child_percentage' => 'required|integer|min:0|max:100',
            'fourth_child_percentage' => 'required|integer|min:0|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        DiscountSetting::current()->update([
            'second_child_percentage' => $validated['second_child_percentage'],
            'third_child_percentage' => $validated['third_child_percentage'],
            'fourth_child_percentage' => $validated['fourth_child_percentage'],
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return back()->with('success', 'Sibling discount settings updated.');
    }
}
