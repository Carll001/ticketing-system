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
        $department->update($data);

        return $department;
    }
}
