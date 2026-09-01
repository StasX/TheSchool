<?php
namespace Tests\Feature;

use App\Models\Administrator;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentTest extends TestCase
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
            'Email'    => 'owner@example.com',
            'Name'     => 'Test Owner',
            'Role'     => 'owner',
            'Phone'    => '0500000000',
            'Password' => Hash::make('password123'),
            'Image'    => '/upload/test.jpg',
        ]);
    }

    private function createStudent(array $attributes = []): Student
    {
        return Student::create(array_merge([
            'Email' => uniqid() . '@example.com',
            'Name'  => 'Test Student',
            'Phone' => '0501234567',
            'Image' => '/upload/test.jpg',
        ], $attributes));
    }

    private function createCourse(array $attributes = []): Course
    {
        return Course::create(array_merge([
            'Name'        => 'Test Course',
            'Description' => 'Test description',
            'Image'       => '/upload/test.jpg',
        ], $attributes));
    }

    public function test_authenticated_administrator_can_get_all_students(): void
    {
        $this->createStudent();
        $this->createStudent();
        $this->createStudent();

        $response = $this
            ->getJson('/api/student');

        $response
            ->assertOk()
            ->assertJsonCount(3);
    }

    public function test_authenticated_administrator_can_get_student_by_id(): void
    {
        $student = $this->createStudent();

        $response = $this
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
            ->getJson('/api/student/999999');

        $response->assertNotFound();
    }

    public function test_authenticated_administrator_can_create_student(): void
    {
        $image = UploadedFile::fake()->image('student.jpg');

        $response = $this
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
        $courses = collect([
            $this->createCourse(['Name' => 'Course A']),
            $this->createCourse(['Name' => 'Course B']),
        ]);

        $response = $this
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
        $this->createStudent([
            'Email' => 'student@example.com',
        ]);

        $response = $this
            ->withHeader('Accept', 'application/json')
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
        $student = $this->createStudent();

        $response = $this
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
        $student = $this->createStudent();

        $oldCourses = collect([
            $this->createCourse(['Name' => 'Old Course A']),
            $this->createCourse(['Name' => 'Old Course B']),
        ]);

        $newCourses = collect([
            $this->createCourse(['Name' => 'New Course A']),
            $this->createCourse(['Name' => 'New Course B']),
        ]);

        $student->courses()->attach(
            $oldCourses->pluck('Course_ID')->all()
        );

        $response = $this
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
        $student = $this->createStudent();

        $response = $this
            ->deleteJson("/api/student/{$student->Student_ID}");

        $response->assertSuccessful();

        $this->assertDatabaseMissing('students', [
            'Student_ID' => $student->Student_ID,
        ]);
    }
}
