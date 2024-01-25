<?php

namespace Models;

use App\Models\Test;
use App\Models\TestType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;

class TestTest extends TestCase
{


    use RefreshDatabase; // If you want to reset your database after each test

        /** @test */
        public function a_test_has_a_test_type()
    {
        // Arrange: Create a TestType and Test instance
        $testType = TestType::factory()->create();
        $test = Test::factory()->create(['test_type_id' => $testType->id]);

        // Act: Retrieve the testType via the relationship
        $retrievedTestType = $test->testType;

        // Assert: Check if the retrieved testType is the one we've created
        $this->assertEquals($testType->id, $retrievedTestType->id);
    }

}
