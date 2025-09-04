<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Tag;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_tag_model_exists()
    {
        $tag = new Tag();
        $this->assertInstanceOf(Tag::class, $tag);
    }

    public function test_tag_has_fillable_attributes()
    {
        $tag = new Tag();
        $fillable = $tag->getFillable();
        
        $this->assertIsArray($fillable);
        $this->assertContains('name', $fillable);
        $this->assertContains('profile_id', $fillable);
    }

    public function test_tag_model_structure()
    {
        // Test model structure
        $tag = new Tag();
        $this->assertInstanceOf(Tag::class, $tag);
    }
}