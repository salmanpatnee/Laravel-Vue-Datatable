<?php

namespace App\Exports;

use App\Models\Student;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsExport implements FromQuery, Responsable, WithHeadings, WithMapping
{
    use Exportable;

    protected $students;

    private $fileName = 'students.xlsx';

    public function headings(): array
    {
        return [
            'id',
            'class',
            'section',
            'name',
            'email',
            'address',
            'phone',
            // 'created_at',
            // 'updated_at',
        ];
    }

    public function map($student): array
    {
        return [
            $student->id,
            $student->class->name,
            $student->section->name,
            $student->name,
            $student->email,
            $student->address,
            $student->phone_number,
            // $student->created_at->toFormattedDateString(),
            // $student->updated_at->toFormattedDateString()
        ];
    }

    public function __construct(array $students)
    {
        $this->students = $students;
    }

    public function query()
    {
        return Student::query()->whereKey($this->students);
    }
}
