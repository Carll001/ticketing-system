<?php

namespace App\Services;

use App\Models\Proof;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class ProofService
{
    public function create(array $data): Proof
    {
        $proof = Proof::create($data);

        return $proof;
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
