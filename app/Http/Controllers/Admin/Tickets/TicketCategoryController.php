<?php

namespace App\Http\Controllers\Admin\Tickets;

use App\Http\Controllers\Controller;
use App\Models\TicketCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class TicketCategoryController extends Controller
{
    public function index()
    {
        return view('admin.ticket-categories.index');
    }

    public function getData()
    {
        $categories = TicketCategory::withCount('tickets')->get();

        return DataTables::of($categories)
            ->addColumn('status_badge', function ($category) {
                $badge = $category->is_active ? 'badge-success' : 'badge-secondary';
                $text = $category->is_active ? 'Active' : 'Inactive';
                return '<span class="badge ' . $badge . '">' . $text . '</span>';
            })
            ->addColumn('color_preview', function ($category) {
                return '<div style="width: 20px; height: 20px; background-color: ' . $category->color . '; border-radius: 3px; display: inline-block;"></div>';
            })
            ->addColumn('actions', function ($category) {
                return '<div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-primary" onclick="editCategory(' . $category->id . ')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteCategory(' . $category->id . ')">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>';
            })
            ->rawColumns(['status_badge', 'color_preview', 'actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        TicketCategory::create([
            'name' => $request->name,
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'color' => $request->color,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully'
        ]);
    }

    public function show($id)
    {
        $category = TicketCategory::findOrFail($id);
        return response()->json(['success' => true, 'data' => $category]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $category = TicketCategory::findOrFail($id);
        $category->update([
            'name' => $request->name,
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'color' => $request->color,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully'
        ]);
    }

    public function destroy($id)
    {
        try {
            $category = TicketCategory::findOrFail($id);
            
            // Check if category has tickets
            if ($category->tickets()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category with existing tickets'
                ], 400);
            }
            
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error occurred while deleting the category'
            ], 500);
        }
    }
}