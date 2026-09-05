<?php

namespace Tests\Feature;

use App\Models\Administrator;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
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
            'Email' => 'owner@example.com',
            'Name' => 'Test Owner',
            'Role' => 'owner',
            'Phone' => '0500000000',
            'Password' => Hash::make('password123'),
            'Image' => '/upload/test.jpg',
        ]);
    }

    private function createStudent(array $attributes = []): Student
    {
        return Student::create(array_merge([
            'Email' => uniqid() . '@example.com',
            'Name' => 'Test Student',
            'Phone' => '0501234567',
            'Image' => '/upload/test.jpg',
        ], $attributes));
    }

    private function createCourse(array $attributes = []): Course
    {
        return Course::create(array_merge([
            'Name' => 'Test Course',
            'Description' => 'Test description',
            'Image' => '/upload/test.jpg',
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
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => [
                    'Student_ID',
                    'Email',
                    'Name',
                    'Phone',
                    'Image',
                    'courses',
                ],
            ]);
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

        $response
            ->assertNotFound()
            ->assertJson([
                'error' => 'Student not found',
            ]);
    }

    public function test_authenticated_administrator_can_create_student(): void
    {
        $image = UploadedFile::fake()->image('student.jpg');

        $response = $this
            ->post('/api/student', [
                'Email' => 'student@example.com',
                'Name' => 'Test Student',
                'Phone' => '0501234567',
                'Image' => $image,
            ]);

        $response->assertCreated();

        $this->assertDatabaseHas('students', [
            'Email' => 'student@example.com',
            'Name' => 'Test Student',
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
                'Email' => 'student@example.com',
                'Name' => 'Test Student',
                'Phone' => '0501234567',
                'Image' => UploadedFile::fake()->image('student.jpg'),
                'courses' => $courses->pluck('Course_ID')->all(),
            ]);

        $response->assertCreated();

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
                'Name' => 'Another Student',
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
                'Name' => 'Updated Student',
                'Phone' => '0509999999',
            ]);

        $response->assertSuccessful();

        $this->assertDatabaseHas('students', [
            'Student_ID' => $student->Student_ID,
            'Email' => 'updated@example.com',
            'Name' => 'Updated Student',
            'Phone' => '0509999999',
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
                'Name' => $student->Name,
                'Phone' => $student->Phone,
                'Email' => $student->Email,
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
        Storage::disk('uploads')->put(
            'student.jpg',
            'student image'
        );

        $student = $this->createStudent([
            'Image' => '/upload/student.jpg',
        ]);

        $response = $this
            ->deleteJson("/api/student/{$student->Student_ID}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('students', [
            'Student_ID' => $student->Student_ID,
        ]);

        $this->assertFalse(
            Storage::disk('uploads')->exists('student.jpg')
        );
    }

    public function test_student_image_can_be_updated(): void
    {
        Storage::disk('uploads')->put(
            'old.jpg',
            'old image'
        );

        $student = $this->createStudent([
            'Image' => '/upload/old.jpg',
        ]);

        $response = $this->post(
            "/api/student/{$student->Student_ID}",
            [
                '_method' => 'PUT',
                'Email' => $student->Email,
                'Name' => $student->Name,
                'Phone' => $student->Phone,
                'Image' => UploadedFile::fake()->image('new.jpg'),
            ]
        );

        $response->assertSuccessful();

        $student->refresh();

        $this->assertTrue(
            Storage::disk('uploads')->exists(
                basename($student->Image)
            )
        );

        $this->assertFalse(
            Storage::disk('uploads')->exists('old.jpg')
        );
    }

    #[DataProvider('requiredStudentFieldsProvider')]
    public function test_required_fields_are_validated_when_creating_student(
        string $field
    ): void {
        $data = [
            'Email' => 'student@example.com',
            'Name' => 'Test Student',
            'Phone' => '0501234567',
            'Image' => UploadedFile::fake()->image('student.jpg'),
        ];

        unset($data[$field]);

        $this->withHeader('Accept', 'application/json')
            ->post('/api/student', $data)
            ->assertUnprocessable()
            ->assertJsonValidationErrors($field);
    }

    public static function requiredStudentFieldsProvider(): array
    {
        return [
            ['Email'],
            ['Name'],
            ['Phone'],
            ['Image'],
        ];
    }

    #[DataProvider('requiredStudentUpdateFieldsProvider')]
    public function test_required_fields_are_validated_when_updating_student(
        string $field
    ): void {
        $student = $this->createStudent();

        $data = [
            'Email' => $student->Email,
            'Name' => $student->Name,
            'Phone' => $student->Phone,
        ];

        unset($data[$field]);

        $this->putJson(
            "/api/student/{$student->Student_ID}",
            $data
        )
            ->assertUnprocessable()
            ->assertJsonValidationErrors($field);
    }

    public static function requiredStudentUpdateFieldsProvider(): array
    {
        return [
            ['Email'],
            ['Name'],
            ['Phone'],
        ];
    }

    public function test_updating_student_without_courses_preserves_existing_courses(): void
    {
        $student = $this->createStudent();

        $courses = collect([
            $this->createCourse(['Name' => 'Course A']),
            $this->createCourse(['Name' => 'Course B']),
        ]);

        $student->courses()->attach(
            $courses->pluck('Course_ID')->all()
        );

        $this->putJson(
            "/api/student/{$student->Student_ID}",
            [
                'Email' => $student->Email,
                'Name' => 'Updated Student',
                'Phone' => $student->Phone,
            ]
        )->assertOk();

        $student->refresh();

        $this->assertEqualsCanonicalizing(
            $courses->pluck('Course_ID')->all(),
            $student->courses->pluck('Course_ID')->all()
        );
    }

    public function test_updating_student_with_empty_courses_removes_all_courses(): void
    {
        $student = $this->createStudent();

        $courses = collect([
            $this->createCourse(['Name' => 'Course A']),
            $this->createCourse(['Name' => 'Course B']),
        ]);

        $student->courses()->attach(
            $courses->pluck('Course_ID')->all()
        );

        $this->putJson(
            "/api/student/{$student->Student_ID}",
            [
                'Email' => $student->Email,
                'Name' => $student->Name,
                'Phone' => $student->Phone,
                'courses' => [],
            ]
        )->assertOk();

        $student->refresh();

        $this->assertCount(0, $student->courses);
    }

    public function test_updating_student_without_image_preserves_image(): void
    {
        Storage::disk('uploads')->put('student.jpg', 'student image');

        $student = $this->createStudent([
            'Image' => '/upload/student.jpg',
        ]);

        $this->putJson(
            "/api/student/{$student->Student_ID}",
            [
                'Email' => $student->Email,
                'Name' => 'Updated Student',
                'Phone' => $student->Phone,
            ]
        )->assertOk();

        $student->refresh();

        $this->assertSame('/upload/student.jpg', $student->Image);

        $this->assertTrue(
            Storage::disk('uploads')->exists('student.jpg')
        );
    }

    public function test_student_image_can_be_updated_when_old_image_is_missing(): void
    {
        $student = $this->createStudent([
            'Image' => '/upload/missing.jpg',
        ]);

        $this->assertFalse(
            Storage::disk('uploads')->exists('missing.jpg')
        );

        $this->post(
            "/api/student/{$student->Student_ID}",
            [
                '_method' => 'PUT',
                'Email' => $student->Email,
                'Name' => $student->Name,
                'Phone' => $student->Phone,
                'Image' => UploadedFile::fake()->image('new.jpg'),
            ]
        )->assertOk();

        $student->refresh();

        $this->assertNotSame('/upload/missing.jpg', $student->Image);

        $this->assertTrue(
            Storage::disk('uploads')->exists(
                basename($student->Image)
            )
        );
    }
}
