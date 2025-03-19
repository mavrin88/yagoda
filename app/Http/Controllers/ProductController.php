<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\RenameProductsNameRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

class ProductController extends Controller
{

    public function index(Category $category)
    {
        $category->products = $category->products()->where('hide', false)->get();

        $category->image = $category->image ? URL::route('image', ['path' => $category->image]) : null;

        $category->products->each(function ($product) {
            $product->image = $product->image ? URL::route('image', ['path' => $product->image]) : null;
        });

        return Inertia::render('SuperAdmin/Products/Index', compact('category'));
    }

    public function addProduct(Category $category)
    {
        $selectedOrganizationId = Session::get('selected_organization_id');

        $categories = Category::where('organization_id', $selectedOrganizationId)->get();

        $categoriesFormated = $categories->map(function ($category) {
            return ['id' => $category->id, 'label' => $category->name, 'value' => $category->id];
        });

        return Inertia::render('SuperAdmin/Products/AddProduct/Index', compact('category', 'categoriesFormated'));
    }

    public function editProduct(Product $product)
    {
        $selectedOrganizationId = Session::get('selected_organization_id');

        $categories = Category::where('organization_id', $selectedOrganizationId)->get();

        $categoriesFormated = $categories->map(function ($category) {
            return ['id' => $category->id, 'label' => $category->name, 'value' => $category->id];
        });

        $product->image = $product->image ? URL::route('image', ['path' => $product->image]) : null;

        return Inertia::render('SuperAdmin/Products/EditProduct/Index', compact('product', 'categoriesFormated'));
    }

    public function renameProductsNames(RenameProductsNameRequest $request)
    {
        $selectedOrganizationId = Session::get('selected_organization_id');

        Product::whereIn('category_id', Category::where('organization_id', $selectedOrganizationId)->pluck('id'))
            ->where('hide', true)
            ->update(['name' => $request->name]);

        return response()->json([
            'success' => true,
            'message' => 'Переименование названий продуктов выполнено'
        ]);

    }

    public function saveProduct(Request $request)
    {
        $product = Product::find($request->id);

        $imagePath = null;

        if (($request->saveImage ?? false)) {
            $base64String = $request->saveImage;

            if (preg_match('/^data:image\/(?<type>jpeg|png);base64,(?<data>.*)$/', $base64String, $matches)) {

                $imageType = $matches['type'];
                $imageData = base64_decode($matches['data']);

                $fileName = 'photo_' . time() . '.' . $imageType;

                Storage::put('products/' . $fileName, $imageData);

                $imagePath = 'products/' . $fileName;
            }
        }
        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'image' => $imagePath
        ]);

        $product->update(['image' => $imagePath]);

        return redirect()->route('super_admin.catalog_products', ['category' => $product->category->id]);
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

                Storage::put('products/' . $fileName, $imageData);

                $imagePath = 'products/' . $fileName;
            }
        }


        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'image' => $imagePath
        ]);

        $product->update(['image' => $imagePath]);

        return redirect()->route('super_admin.catalog_products', ['category' => $product->category->id]);
    }

    public function hide(Request $request)
    {
        try {
            if (!$request->has('product_id')) {
                throw new \InvalidArgumentException('Не указан идентификатор продукта.');
            }

            $product = Product::find($request->product_id);

            if (!$product) {
                throw new \Exception('Продукт с таким ID не найден.');
            }

            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Продукт успешно скрыт.'
            ]);

        } catch (\Exception $e) {

            throw $e;
        }
    }


}
