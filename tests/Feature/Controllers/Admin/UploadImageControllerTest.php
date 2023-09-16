<?php

namespace Tests\Feature\Controllers\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class UploadImageControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $middlewares = [
        'web', 'admin'
    ];

    /**
     * A basic feature test example.
     */
    public function testUploadMethodCanUploadImage(): void
    {
        $image = UploadedFile::fake()->image('image.png');

        $this->actingAs(User::factory()->admin()->create())
            ->withHeaders([
                'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
            ])
            ->postJson(route('upload'), compact('image'))
            ->assertOk()
            ->assertJson(['url' => "/upload/{$image->hashName()}"]);
        $this->assertFileExists(public_path("/upload/{$image->hashName()}"));

        $this->assertEquals(
            request()->route()->middleware(),
            $this->middlewares
        );
    }

    public function testUploadMethodValidationRequestImageDataHasImageRule(): void
    {
        $image = UploadedFile::fake()->create('image.text');

        $this->actingAs(User::factory()->admin()->create())
            ->withHeaders([
                'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
            ])
            ->postJson(route('upload'), compact('image'))
            ->assertJsonValidationErrors([
                'image'=>"The image field must be an image."
            ]);
        $this->assertFileDoesNotExist(public_path("/upload/{$image->hashName()}"));

        $this->assertEquals(
            request()->route()->middleware(),
            $this->middlewares
        );
    }

    public function testUploadMethodValidationRequestImageDataHasMaximumSizeRule(): void
    {
        $image = UploadedFile::fake()->create('image.text',251);

        $this->actingAs(User::factory()->admin()->create())
            ->withHeaders([
                'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
            ])
            ->postJson(route('upload'), compact('image'))
            ->assertJsonValidationErrors([
                'image'=>"The image field must not be greater than 250 kilobytes."
            ]);
        $this->assertFileDoesNotExist(public_path("/upload/{$image->hashName()}"));

        $this->assertEquals(
            request()->route()->middleware(),
            $this->middlewares
        );
    }

    public function testUploadMethodValidationRequestImageDataHasMaximumDimensionsRule(): void
    {
        $image = UploadedFile::fake()->image('image.text',101,201)->size(50);

        $this->actingAs(User::factory()->admin()->create())
            ->withHeaders([
                'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
            ])
            ->postJson(route('upload'), compact('image'))
            ->assertJsonValidationErrors([
                'image'=>"The image field has invalid image dimensions."
            ]);
        $this->assertFileDoesNotExist(public_path("/upload/{$image->hashName()}"));

        $this->assertEquals(
            request()->route()->middleware(),
            $this->middlewares
        );
    }



}
