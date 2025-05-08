<?php

namespace App\Domain\Inventory\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

interface ProductRepositoryInterface
{
    public function index(Request $request): LengthAwarePaginator;
}
