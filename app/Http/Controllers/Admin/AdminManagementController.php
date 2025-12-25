<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminManagementController extends Controller
{
    /**
     * Display admin management page
     */
    public function index()
    {
        $admins = Admin::orderBy('admin_id')->get();
        return view('admin.management', compact('admins'));
    }

    /**
     * Display admin detail page
     */
    public function show($id)
    {
        $selectedAdmin = Admin::findOrFail($id);
        $admins = Admin::orderBy('admin_id')->get();
        return view('admin.detail', compact('selectedAdmin', 'admins'));
    }

    /**
     * Get all admins as JSON
     */
    public function getAdmins()
    {
        $admins = Admin::orderBy('admin_id')->get();
        return response()->json($admins);
    }

    /**
     * Store a new admin
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'admin_id' => 'required|string|unique:admins,admin_id|regex:/^ADMIN\d{3}$/',
            'computer_number' => 'required|integer|min:0',
            'password' => 'required|string|min:8|max:8',
        ], [
            'admin_id.regex' => 'Admin ID must be in format ADMIN001',
            'password.min' => 'Password must be exactly 8 digits',
            'password.max' => 'Password must be exactly 8 digits',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $admin = Admin::create([
            'admin_id' => $request->admin_id,
            'computer_number' => $request->computer_number,
            'password' => Hash::make($request->password),
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Admin added successfully',
            'admin' => $admin
        ]);
    }

    /**
     * Update admin status
     */
    public function updateStatus(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $admin->status = $request->status;
        $admin->save();

        return response()->json([
            'success' => true,
            'message' => 'Admin status updated successfully',
            'admin' => $admin
        ]);
    }

    /**
     * Delete an admin
     */
    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);
        
        // Prevent deleting the last admin
        if (Admin::count() <= 1) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete the last admin account'
            ], 422);
        }

        $admin->delete();

        return response()->json([
            'success' => true,
            'message' => 'Admin deleted successfully'
        ]);
    }

    /**
     * Update admin password
     */
    public function updatePassword(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|max:8',
        ], [
            'password.min' => 'Password must be exactly 8 digits',
            'password.max' => 'Password must be exactly 8 digits',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $admin->password = Hash::make($request->password);
        $admin->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully'
        ]);
    }
}
