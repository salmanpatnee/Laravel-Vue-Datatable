<?php

namespace App\Http\Controllers;

use App\Exports\StudentsExport;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $paginate   = request('paginate', 10);
        $term       = request('search', '');
        $sortOrder  = request('sortOrder');
        $orderBy    = request('orderBy');

        $students = Student::with(['class', 'section'])
            ->search($term)
            ->orderBy($orderBy, $sortOrder)
            ->paginate($paginate);
        return  StudentResource::collection($students);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function selectAll()
    {
        return Student::pluck('id');
    }

    public function export($students)
    {
        $students = explode(',', $students);

        return (new StudentsExport($students));
    }
}
