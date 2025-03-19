<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $selectedOrganizationId = Session::get('selected_organization_id');

        $products = Product::whereHas('category', function($query) use ($selectedOrganizationId) {
            $query->where('organization_id', $selectedOrganizationId);
        })
            ->where('hide', true)
            ->first();

        return Inertia::render('SuperAdmin/Category/Index', compact('products'));
    }

    public function store(Request $request)
    {
        $imagePath = null;

        if (($request->saveImage ?? false)) {
            $base64String = $request->saveImage;

            if (preg_match('/^data:image\/(?<type>jpeg|png);base64,(?<data>.*)$/', $base64String, $matches)) {

                $imageType = $matches['type'];
                $imageData = base64_decode($matches['data']);

                $fileName = 'photo_' . time() . '.' . $imageType;

                Storage::put('categories/' . $fileName, $imageData);

                $imagePath = 'categories/' . $fileName;
            }
        }

        $selectedOrganizationId = Session::get('selected_organization_id');

        Category::create([
            'name' => $request->name,
            'image' => $imagePath,
            'bill_id' => $request->bill_id['id'],
            'organization_id' => $selectedOrganizationId
        ]);

        return redirect()->route('super_admin.categories');
    }

    public function showCategories()
    {
        $selectedOrganizationId = Session::get('selected_organization_id');

        $categories = Category::where('organization_id', $selectedOrganizationId)->get();

        $categories->each(function ($category) {
            $category->image = $category->image ? URL::route('image', ['path' => $category->image, 'w' => 72, 'h' => 72, 'fit' => 'crop']) : null;
        });

        return Inertia::render('SuperAdmin/Category/ShowCategory/Index', compact('categories'));
    }

    public function addCategory()
    {
        $selectedOrganizationId = Session::get('selected_organization_id');

        $bills = Bill::where('organization_id', $selectedOrganizationId)->get();

        $bills = $bills->map(function ($bill) {
            return ['id' => $bill->id, 'label' => $bill->name, 'value' => $bill->id];
        });

        return Inertia::render('SuperAdmin/Category/AddCategory/Index', compact('bills'));
    }

    public function editCategory(Category $category)
    {
        $selectedOrganizationId = Session::get('selected_organization_id');

        $bills = Bill::where('organization_id', $selectedOrganizationId)->get();

        $bills = $bills->map(function ($bill) {
            return ['id' => $bill->id, 'label' => $bill->name, 'value' => $bill->id];
        });

        $category->image = $category->image ? URL::route('image', ['path' => $category->image]) : null;

        return Inertia::render('SuperAdmin/Category/EditCategory/Index', compact('category', 'bills'));
    }

    public function delete(Request $request)
    {
        $category = Category::find($request->id);
        $category->delete();

        return redirect()->route('super_admin.categories');
    }

    public function saveCategory(Request $request)
    {
        $category = Category::find($request->id);

        $imagePath = $category->image;

        if (($request->saveImage ?? false)) {
            $base64String = $request->saveImage;

            if (preg_match('/^data:image\/(?<type>jpeg|png);base64,(?<data>.*)$/', $base64String, $matches)) {

                $imageType = $matches['type'];
                $imageData = base64_decode($matches['data']);

                $fileName = 'photo_' . time() . '.' . $imageType;

                Storage::put('categories/' . $fileName, $imageData);

                $imagePath = 'categories/' . $fileName;
            }
        }

        $category->update([
            'name' => $request->name,
            'bill_id' => $request->bill_id,
            'image' => $imagePath,
        ]);

        $category->update(['image' => $imagePath]);

        return redirect()->route('super_admin.categories');
    }
}
