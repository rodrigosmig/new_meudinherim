<?php

namespace Tests\Feature\Api;

use App\Models\AccountEntry;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;

class CategoryTest extends TestCase
{
    protected $user;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function testGetCategoriesWithUnauthenticatedUser()
    {
        $response = $this->getJson('/api/categories', []);

        $response->assertStatus(401)
                ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testValidationErrorWhenCreatingACategory()
    {
        Sanctum::actingAs(
            $this->user
        );
        
        $data = [
            'type' => 'Fake Type',
            'name' => 'Expense Category'
        ];

        $message = __('validation.in', ['attribute' => 'type']);

        $response = $this->postJson('/api/categories', $data);

        $response->assertStatus(422)
                ->assertExactJson(['type' =>[$message]]);
    }

    public function testCreateCategoryWhenUnauthenticatedUser()
    {
        $response = $this->postJson('/api/categories', []);

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testCreateCategorySuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );
        
        $data = [
            'name' => 'Expense Category',
            'type' => Category::EXPENSE,            
        ];

        $response = $this->postJson('/api/categories', $data);

        $response->assertStatus(201)
            ->assertJsonPath('type', $data['type'])
            ->assertJsonPath('name', $data['name']);
    }
    

    public function testGetCategoriesSuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $income_categories  = Category::factory()->count(3)->create(['type' => Category::INCOME]);
        $expense_category = Category::factory()->create(['type' => Category::EXPENSE]);

        $response = $this->getJson('/api/categories', []);

        $response->assertStatus(200)
            ->assertJsonCount(4, 'data');
    }

    public function testGetOnlyIncomeCategories()
    {
        Sanctum::actingAs(
            $this->user
        );

        $income_categories  = Category::factory()->count(3)->create(['type' => Category::INCOME]);
        $expense_category = Category::factory()->count(3)->create(['type' => Category::EXPENSE]);

        $response = $this->getJson('/api/categories?type=1', []);

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function testGetOnlyExpenseCategories()
    {
        Sanctum::actingAs(
            $this->user
        );

        $income_categories  = Category::factory()->count(3)->create(['type' => Category::INCOME]);
        $expense_category = Category::factory()->count(5)->create(['type' => Category::EXPENSE]);

        $response = $this->getJson('/api/categories?type=2', []);

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function testGetNonExistentCategory()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = 'Invalid Category';

        $response = $this->getJson("/api/categories/{$category}");

        $response->assertStatus(404);
    }

    public function testGetCategoryFromAnotherUser()
    {
        $user = User::factory()->create();

        $category = Category::createWithoutEvents([
            'name'      => 'Category Test',
            'type'      => Category::INCOME,
            'user_id'   => $user->id
        ]);

        Sanctum::actingAs(
            $this->user
        );

        $response = $this->getJson("/api/categories/{$category->id}");

        $response->assertStatus(404);
    }

    public function testGetACategorySuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = Category::factory()->create();

        $response = $this->getJson("/api/categories/{$category->id}");

        $response->assertStatus(200)
                ->assertJsonPath('id', $category->id)
                ->assertJsonPath('name', $category->name);
    }

    public function testUpdateCategoryWithUnauthenticatedUser()
    {
        $category = 'category';

        $response = $this->putJson("/api/categories/{$category}", []);

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testUpdateNonExistentCategory()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = 'category';

        $data = [
            'name' => 'Update Category',
            'type' => Category::INCOME
        ];

        $response = $this->putJson("/api/categories/{$category}", $data);

        $response->assertStatus(404);
    }

    public function testValidationErrorWhenUpdateACategory()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = Category::factory()->create(['type' => Category::EXPENSE]);
        
        $message_type = __('validation.in', ['attribute' => 'type']);
        $message_name = __('validation.min.string', ['attribute' => __('validation.attributes.name'), 'min' => 3]);

        $data = [
            'name' => 'T',
            'type' => 'fake type'
        ];

        $response = $this->putJson("/api/categories/{$category->id}", $data);

        $response->assertStatus(422)
                ->assertJsonPath('type', [$message_type])
                ->assertJsonPath('name', [$message_name]);
    }

    public function testUpdateACategorySuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = Category::factory()->create(['type' => Category::EXPENSE]);

        $data = [
            'name' => 'Update Category',
            'type' => Category::INCOME
        ];

        $response = $this->putJson("/api/categories/{$category->id}", $data);

        $response->assertStatus(200)
                ->assertJsonPath('name', $data['name'])
                ->assertJsonPath('type', $data['type']);
    }

    public function testDeleteCategoryWithUnauthenticatedUser()
    {
        $category = 'category';

        $response = $this->putJson("/api/categories/{$category}");

        $response->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testDeleteNonExistentCategory()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = 'category';

        $response = $this->deleteJson("/api/categories/{$category}");

        $response->assertStatus(404);
    }

    public function testFailedToDeleteACategoryAssociatedWithAccountEntry()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = Category::factory()->create(['type' => Category::INCOME]);
        
        AccountEntry::factory()->create(['category_id' => $category->id]);

        $message = __('messages.categories.not_delete');

        $response = $this->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(400)
                ->assertJsonPath('message', $message);
    }

    public function testDeleteCategorySuccessfully()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = Category::factory()->create(['type' => Category::INCOME]);

        $response = $this->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(204);
    }

    public function testGetInactiveCategories()
    {
        Sanctum::actingAs(
            $this->user
        );

        Category::factory()->count(2)->create([
            'type'      => Category::INCOME,
            'active'    => false
        ]);

        $response = $this->getJson("/api/categories?active=false");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }
    
    public function testGetActiveCategories()
    {
        Sanctum::actingAs(
            $this->user
        );

        Category::factory()->create([
            'type'      => Category::INCOME,
            'active'    => false
        ]);

        Category::factory()->create([
            'type'      => Category::EXPENSE,
            'active'    => false
        ]);

        Category::factory()->count(2)->create([
            'type'      => Category::INCOME
        ]);

        Category::factory()->count(2)->create([
            'type'      => Category::EXPENSE
        ]);

        $response = $this->getJson("/api/categories?active=true");

        $response->assertStatus(200)
            ->assertJsonCount(4, 'data');
    }
}
