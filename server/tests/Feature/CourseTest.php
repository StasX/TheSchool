<?php
namespace Tests\Feature;

use App\Models\Administrator;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    private Administrator $administrator;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('uploads');

        $this->administrator = Administrator::factory()->create([
            'Role' => 'owner',
        ]);
    }

    public function test_authenticated_administrator_can_get_all_courses(): void
    {
        Course::factory()->count(3)->create();

        $response = $this
            ->actingAs($this->administrator)
            ->getJson('/api/course');

        $response
            ->assertOk()
            ->assertJsonCount(3);
    }

    public function test_authenticated_administrator_can_get_course_by_id(): void
    {
        $course = Course::factory()->create();

        $response = $this
            ->actingAs($this->administrator)
            ->getJson("/api/course/{$course->Course_ID}");

        $response
            ->assertOk()
            ->assertJsonPath('Course_ID', $course->Course_ID)
            ->assertJsonPath('Name', $course->Name)
            ->assertJsonPath('Description', $course->Description);
    }

    public function test_get_non_existing_course_returns_404(): void
    {
        $response = $this
            ->actingAs($this->administrator)
            ->getJson('/api/course/999999');

        $response->assertNotFound();
    }

    public function test_authenticated_administrator_can_create_course(): void
    {
        $image = UploadedFile::fake()->image('course.jpg');

        $response = $this
            ->actingAs($this->administrator)
            ->post('/api/course', [
                'Name'        => 'PHP Course',
                'Description' => 'Laravel backend course',
                'Image'       => $image,
            ]);

        $response->assertSuccessful();

        $this->assertDatabaseHas('courses', [
            'Name'        => 'PHP Course',
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
            ->actingAs($this->administrator)
            ->post('/api/course', [
                'Description' => 'Test description',
                'Image'       => UploadedFile::fake()->image('course.jpg'),
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
            ->actingAs($this->administrator)
            ->post('/api/course', [
                'Name'        => 'PHP Course',
                'Description' => 'Test description',
                'Image'       => $file,
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('Image');
    }

    public function test_authenticated_administrator_can_update_course(): void
    {
        $course = Course::factory()->create();

        $response = $this
            ->actingAs($this->administrator)
            ->putJson("/api/course/{$course->Course_ID}", [
                'Name'        => 'Updated Course',
                'Description' => 'Updated description',
            ]);

        $response->assertSuccessful();

        $this->assertDatabaseHas('courses', [
            'Course_ID'   => $course->Course_ID,
            'Name'        => 'Updated Course',
            'Description' => 'Updated description',
        ]);
    }

    public function test_course_image_can_be_updated(): void
    {
        $course = Course::factory()->create([
            'Image' => '/upload/old.jpg',
        ]);

        Storage::disk('uploads')->put(
            'old.jpg',
            'old image'
        );

        $newImage = UploadedFile::fake()->image('new.jpg');

        $response = $this
            ->actingAs($this->administrator)
            ->post("/api/course/{$course->Course_ID}", [
                '_method' => 'PUT',
                'Image'   => $newImage,
            ]);

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
        $course = Course::factory()->create();

        $response = $this
            ->actingAs($this->administrator)
            ->deleteJson("/api/course/{$course->Course_ID}");

        $response->assertSuccessful();

        $this->assertDatabaseMissing('courses', [
            'Course_ID' => $course->Course_ID,
        ]);
    }
}
