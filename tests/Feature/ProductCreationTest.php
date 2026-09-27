<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tax;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_creation_persists_brand_and_uploads_image_to_r2(): void
    {
        Storage::fake('r2');

        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $brand = Brand::create([
            'name' => 'Marca prueba',
            'slug' => 'marca-prueba',
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Categoría prueba',
            'slug' => 'categoria-prueba',
            'is_active' => true,
        ]);

        $tax = Tax::create([
            'name' => 'IVA',
            'rate' => 16,
            'is_active' => true,
        ]);

        $response = $this->post(route('products.store'), [
            'name' => 'Producto prueba',
            'description' => 'Descripcion de prueba',
            'price' => 99.99,
            'stock' => 10,
            'sku' => 'PROD-TEST-001',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'tax_id' => $tax->id,
            'is_active' => '1',
            'is_featured' => '1',
            'image' => UploadedFile::fake()->image('product.jpg', 600, 600),
        ]);

        $response->assertRedirect(route('products.index'));

        $product = Product::where('name', 'Producto prueba')->firstOrFail();

        $this->assertSame($brand->id, $product->brand_id);
        $this->assertNotEmpty($product->image);
        Storage::disk('r2')->assertExists($product->image);
    }
}
