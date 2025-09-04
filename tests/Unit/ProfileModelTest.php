<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Profile;

class ProfileModelTest extends TestCase
{
    public function test_profile_model_exists()
    {
        $profile = new Profile();
        $this->assertInstanceOf(Profile::class, $profile);
    }

    public function test_profile_has_fillable_attributes()
    {
        $fillableAttributes = [
            'user_id',
            'name',
            'email',
            'linked_profile_count'
        ];

        $profile = new Profile();
        $this->assertEquals($fillableAttributes, $profile->getFillable());
    }
}