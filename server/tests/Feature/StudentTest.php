<?php
namespace Tests\Feature;

use App\Models\Administrator;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentTest extends TestCase
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

    public function test_authenticated_administrator_can_get_all_students(): void
    {
        Student::factory()->count(3)->create();

        $response = $this
            ->actingAs($this->administrator)
            ->getJson('/api/student');

        $response
            ->assertOk()
            ->assertJsonCount(3);
    }

    public function test_authenticated_administrator_can_get_student_by_id(): void
    {
        $student = Student::factory()->create();

        $response = $this
            ->actingAs($this->administrator)
            ->getJson("/api/student/{$student->Student_ID}");

        $response
            ->assertOk()
            ->assertJsonPath('Student_ID', $student->Student_ID)
            ->assertJsonPath('Email', $student->Email)
            ->assertJsonPath('Name', $student->Name);
    }

    public function test_get_non_existing_student_returns_404(): void
    {
        $response = $this
            ->actingAs($this->administrator)
            ->getJson('/api/student/999999');

        $response->assertNotFound();
    }

    public function test_authenticated_administrator_can_create_student(): void
    {
        $image = UploadedFile::fake()->image('student.jpg');

        $response = $this
            ->actingAs($this->administrator)
            ->post('/api/student', [
                'Email' => 'student@example.com',
                'Name'  => 'Test Student',
                'Phone' => '0501234567',
                'Image' => $image,
            ]);

        $response->assertSuccessful();

        $this->assertDatabaseHas('students', [
            'Email' => 'student@example.com',
            'Name'  => 'Test Student',
            'Phone' => '0501234567',
        ]);

        $student = Student::where('Email', 'student@example.com')->firstOrFail();

        $this->assertNotEmpty($student->Image);

        $this->assertTrue(
            Storage::disk('uploads')->exists(
                basename($student->Image)
            )
        );
    }

    public function test_student_can_be_created_with_courses(): void
    {
        $courses = Course::factory()->count(2)->create();

        $response = $this
            ->actingAs($this->administrator)
            ->post('/api/student', [
                'Email'   => 'student@example.com',
                'Name'    => 'Test Student',
                'Phone'   => '0501234567',
                'Image'   => UploadedFile::fake()->image('student.jpg'),
                'courses' => $courses->pluck('Course_ID')->all(),
            ]);

        $response->assertSuccessful();

        $student = Student::where('Email', 'student@example.com')->firstOrFail();

        $this->assertCount(2, $student->courses);

        $this->assertEqualsCanonicalizing(
            $courses->pluck('Course_ID')->all(),
            $student->courses->pluck('Course_ID')->all()
        );
    }

    public function test_email_must_be_unique_when_creating_student(): void
    {
        Student::factory()->create([
            'Email' => 'student@example.com',
        ]);

        $response = $this
            ->actingAs($this->administrator)
            ->post('/api/student', [
                'Email' => 'student@example.com',
                'Name'  => 'Another Student',
                'Phone' => '0501234567',
                'Image' => UploadedFile::fake()->image('student.jpg'),
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('Email');
    }

    public function test_authenticated_administrator_can_update_student(): void
    {
        $student = Student::factory()->create();

        $response = $this
            ->actingAs($this->administrator)
            ->putJson("/api/student/{$student->Student_ID}", [
                'Email' => 'updated@example.com',
                'Name'  => 'Updated Student',
                'Phone' => '0509999999',
            ]);

        $response->assertSuccessful();

        $this->assertDatabaseHas('students', [
            'Student_ID' => $student->Student_ID,
            'Email'      => 'updated@example.com',
            'Name'       => 'Updated Student',
            'Phone'      => '0509999999',
        ]);
    }

    public function test_student_courses_can_be_updated(): void
    {
        $student = Student::factory()->create();

        $oldCourses = Course::factory()->count(2)->create();
        $newCourses = Course::factory()->count(2)->create();

        $student->courses()->attach(
            $oldCourses->pluck('Course_ID')->all()
        );

        $response = $this
            ->actingAs($this->administrator)
            ->putJson("/api/student/{$student->Student_ID}", [
                'courses' => $newCourses->pluck('Course_ID')->all(),
            ]);

        $response->assertSuccessful();

        $student->refresh();

        $this->assertEqualsCanonicalizing(
            $newCourses->pluck('Course_ID')->all(),
            $student->courses->pluck('Course_ID')->all()
        );
    }

    public function test_authenticated_administrator_can_delete_student(): void
    {
        $student = Student::factory()->create();

        $response = $this
            ->actingAs($this->administrator)
            ->deleteJson("/api/student/{$student->Student_ID}");

        $response->assertSuccessful();

        $this->assertDatabaseMissing('students', [
            'Student_ID' => $student->Student_ID,
        ]);
    }
}
