<?php

namespace Tests\Feature;

use App\Models\Administrator;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    private Administrator $owner;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('uploads');

        $this->owner = $this->getOwner();
        $this->actingAs($this->owner);
    }

    private function getOwner(): Administrator
    {
        return Administrator::where('Role', 'owner')->first() ?? Administrator::create([
            'Email' => 'owner@example.com',
            'Name' => 'Test Owner',
            'Role' => 'owner',
            'Phone' => '0500000000',
            'Password' => Hash::make('password123'),
            'Image' => '/upload/test.jpg',
        ]);
    }

    private function createCourse(array $attributes = []): Course
    {
        return Course::create(array_merge([
            'Name' => 'Test Course',
            'Description' => 'Test description',
            'Image' => '/upload/test.jpg',
        ], $attributes));
    }

    public function test_authenticated_administrator_can_get_all_courses(): void
    {
        $this->createCourse(['Name' => 'Course A']);
        $this->createCourse(['Name' => 'Course B']);
        $this->createCourse(['Name' => 'Course C']);

        $response = $this->getJson('/api/course');

        $response
            ->assertOk()
            ->assertJsonCount(3);
    }

    public function test_authenticated_administrator_can_get_course_by_id(): void
    {
        $course = $this->createCourse();

        $response = $this->getJson(
            "/api/course/{$course->Course_ID}"
        );

        $response
            ->assertOk()
            ->assertJsonPath('Course_ID', $course->Course_ID)
            ->assertJsonPath('Name', $course->Name)
            ->assertJsonPath('Description', $course->Description);
    }

    public function test_get_non_existing_course_returns_404(): void
    {
        $response = $this
            ->getJson('/api/course/999999');

        $response->assertNotFound();
    }

    public function test_authenticated_administrator_can_create_course(): void
    {
        $image = UploadedFile::fake()->image('course.jpg');

        $response = $this
            ->post('/api/course', [
                'Name' => 'PHP Course',
                'Description' => 'Laravel backend course',
                'Image' => $image,
            ]);

        $response->assertSuccessful();

        $this->assertDatabaseHas('courses', [
            'Name' => 'PHP Course',
            'Description' => 'Laravel backend course',
        ]);

        $course = Course::where('Name', 'PHP Course')->firstOrFail();

        $this->assertNotEmpty($course->Image);

        $this->assertTrue(
            Storage::disk('uploads')->exists(
                basename($course->Image)
            )
        );
    }

    public function test_name_is_required_when_creating_course(): void
    {
        $response = $this
            ->withHeader('Accept', 'application/json')
            ->post('/api/course', [
                'Description' => 'Test description',
                'Image' => UploadedFile::fake()->image('course.jpg'),
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('Name');
    }

    public function test_image_must_be_valid_image(): void
    {
        $file = UploadedFile::fake()->create(
            'course.txt',
            100,
            'text/plain'
        );

        $response = $this
            ->withHeader('Accept', 'application/json')
            ->post('/api/course', [
                'Name' => 'PHP Course',
                'Description' => 'Test description',
                'Image' => $file,
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('Image');
    }

    public function test_authenticated_administrator_can_update_course(): void
    {
        $course = $this->createCourse();

        $response = $this->putJson(
            "/api/course/{$course->Course_ID}",
            [
                'Name' => 'Updated Course',
                'Description' => 'Updated description',
            ]
        );

        $response->assertSuccessful();

        $this->assertDatabaseHas('courses', [
            'Course_ID' => $course->Course_ID,
            'Name' => 'Updated Course',
            'Description' => 'Updated description',
        ]);
    }

    public function test_course_image_can_be_updated(): void
    {
        $course = $this->createCourse([
            'Image' => '/upload/old.jpg',
        ]);

        Storage::disk('uploads')->put(
            'old.jpg',
            'old image'
        );

        $newImage = UploadedFile::fake()->image('new.jpg');

        $response = $this->post(
            "/api/course/{$course->Course_ID}",
            [
                '_method' => 'PUT',
                'Name' => $course->Name,
                'Description' => $course->Description,
                'Image' => $newImage,
            ]
        );

        $response->assertSuccessful();

        $course->refresh();

        $this->assertTrue(
            Storage::disk('uploads')->exists(
                basename($course->Image)
            )
        );
    }

    public function test_authenticated_administrator_can_delete_course(): void
    {
        $course = $this->createCourse();

        $response = $this->deleteJson(
            "/api/course/{$course->Course_ID}"
        );

        $response->assertSuccessful();

        $this->assertDatabaseMissing('courses', [
            'Course_ID' => $course->Course_ID,
        ]);
    }
}
