<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'name', 'description', 'image'];

    public function getResults($data, $totalPage)
    {
        if(!isset($data['filter']) && !isset($data['name']) && !isset($data['description'])) {
            return $this->paginate($totalPage);
        }

        return $this->where(function ($query) use ($data) {
            if(isset($data['filter'])) {
                $filter = $data['filter'];
                $query->where('name', $filter);
                $query->orWhere('description', 'LIKE', "%$filter%");
            } else if(isset($data['name'])) {
                $query->where('name', $data['name']);
            } else if(isset($data['description'])) {
                $description = $data['description'];
                $query->where('description', 'LIKE', "%$description%");
            }
        })->paginate($totalPage);
    }
}
