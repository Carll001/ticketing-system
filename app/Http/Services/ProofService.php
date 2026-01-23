<?php

namespace App\Services;

use App\Models\Proof;

class ProofService
{
    public function create(array $data): Proof
    {
        return Proof::create($data);
    }

    public function update(Proof $proof, array $data): Proof
    {
        $proof->update($data);
        return $proof;
    }

    public function delete(Proof $proof): void
    {
        $proof->delete();
    }
}
