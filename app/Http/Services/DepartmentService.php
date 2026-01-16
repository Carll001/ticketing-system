<?php

namespace App\Http\Services;

use App\Models\Department;

class DepartmentService
{

    public function store(array $data)
    {
        return Department::create([
            'name' => $data['name'],
        ]);
    }


    public function update(array $data, Department $department)
    {
        // dd('update');

        $department->update($data);

        return $department;
    }
}
