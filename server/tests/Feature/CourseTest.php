<?php

namespace Tests\Feature;

use App\Models\Administrator;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
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
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => [
                    'Course_ID',
                    'Name',
                    'Description',
                    'Image',
                    'students',
                ],
            ]);
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
        $this->getJson('/api/course/999999')
            ->assertNotFound()
            ->assertJson([
                'error' => 'Course not found',
            ]);
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

        $response->assertCreated();

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
            'Image' => '/upload/course.jpg',
        ]);

        Storage::disk('uploads')->put(
            'course.jpg',
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

        $this->assertFalse(
            Storage::disk('uploads')->exists('course.jpg')
        );
    }

    public function test_authenticated_administrator_can_delete_course(): void
    {
        Storage::disk('uploads')->put(
            'course.jpg',
            'course image'
        );

        $course = $this->createCourse([
            'Image' => '/upload/course.jpg',
        ]);

        $response = $this->deleteJson(
            "/api/course/{$course->Course_ID}"
        );

        $response->assertNoContent();

        $this->assertDatabaseMissing('courses', [
            'Course_ID' => $course->Course_ID,
        ]);

        $this->assertFalse(
            Storage::disk('uploads')->exists('course.jpg')
        );
    }

    #[DataProvider('requiredCourseFieldsProvider')]
    public function test_required_fields_are_validated_when_creating_course(
        string $field
    ): void {
        $data = [
            'Name' => 'PHP Course',
            'Description' => 'Laravel backend course',
            'Image' => UploadedFile::fake()->image('course.jpg'),
        ];

        unset($data[$field]);

        $this->withHeader('Accept', 'application/json')
            ->post('/api/course', $data)
            ->assertUnprocessable()
            ->assertJsonValidationErrors($field);
    }

    public static function requiredCourseFieldsProvider(): array
    {
        return [
            ['Name'],
            ['Description'],
            ['Image'],
        ];
    }

    #[DataProvider('requiredCourseUpdateFieldsProvider')]
    public function test_required_fields_are_validated_when_updating_course(
        string $field
    ): void {
        $course = $this->createCourse();

        $data = [
            'Name' => 'Updated Course',
            'Description' => 'Updated description',
        ];

        unset($data[$field]);

        $this->putJson(
            "/api/course/{$course->Course_ID}",
            $data
        )
            ->assertUnprocessable()
            ->assertJsonValidationErrors($field);
    }

    public static function requiredCourseUpdateFieldsProvider(): array
    {
        return [
            ['Name'],
            ['Description'],
        ];
    }

        public function test_update_non_existing_course_returns_404(): void
    {
        $this->putJson('/api/course/999999', [
            'Name' => 'Updated Course',
            'Description' => 'Updated description',
        ])
            ->assertNotFound()
            ->assertJson([
                'error' => 'Course not found',
            ]);
    }

    public function test_delete_non_existing_course_returns_404(): void
    {
        $this->deleteJson('/api/course/999999')
            ->assertNotFound()
            ->assertJson([
                'error' => 'Course not found',
            ]);
    }
}
