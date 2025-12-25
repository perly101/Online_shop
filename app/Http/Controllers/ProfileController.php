<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index');
    }

    public function updateName(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            auth()->user()->update([
                'name' => $request->name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Name updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update name. Please try again.'
            ], 400);
        }
    }

    public function updatePhone(Request $request)
    {
        try {
            $request->validate([
                'phone' => 'required|string|max:20',
            ]);

            auth()->user()->update([
                'phone' => $request->phone,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Phone updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update phone. Please try again.'
            ], 400);
        }
    }

    public function addEmail(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|unique:users,email,' . auth()->id(),
            ]);

            auth()->user()->update([
                'email' => $request->email,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Email updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update email. Please try again.'
            ], 400);
        }
    }
}
